<?php

/**
 * A Magento 2 module named Experius/Postcode
 * Copyright (C) 2016 Experius
 *
 * This file included in Experius/Postcode is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Experius\Postcode\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const API_TIMEOUT = 3;
    const API_URL = 'https://api.postcode.eu';

    const EXCEPTION_NOT_AUTHORIZED = 'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_NotAuthorizedException';
    const EXCEPTION_PASSWORD_NOT_CORRECT =
        'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_PasswordNotCorrectException';

    protected $modules = null;

    protected $enrichType = 0;

    protected $httpResponseRaw = null;
    protected $httpResponseCode = null;
    protected $httpResponseCodeClass = null;
    protected $httpClientError = null;
    protected $debuggingOverride = false;

    protected $productMetadataInterface;

    protected $moduleList;

    protected $developerHelper;

    public function __construct(
        ProductMetadataInterface $productMetadataInterface,
        ModuleListInterface $moduleList,
        \Magento\Developer\Helper\Data $developerHelper,
        Context $context
    ) {
        $this->productMetadataInterface = $productMetadataInterface;
        $this->moduleList = $moduleList;
        $this->developerHelper = $developerHelper;
        parent::__construct($context);
    }

    /**
     * Get the html for initializing validation script.
     *
     * @param bool $getAdminConfig
     *
     * @return array
     */
    public function getJsinit($getAdminConfig = false)
    {
        if ($getAdminConfig && !$this->getStoreConfig('postcodenl_api/advanced_config/admin_validation_enabled')) {
            return [];
        }

        $useStreet2AsHousenumber = $this->getStoreConfig('postcodenl_api/advanced_config/use_street2_as_housenumber');
        $useStreet3AsHousenumberAddition = $this->getConfigBoolString(
            'postcodenl_api/advanced_config/use_street3_as_housenumber_addition'
        );
        $neverHideCountry = $this->getConfigBoolString('postcodenl_api/advanced_config/never_hide_country');
        return [
            "useStreet2AsHouseNumber" => (boolean)$useStreet2AsHousenumber,
            "useStreet3AsHouseNumberAddition" => $useStreet3AsHousenumberAddition,
            "neverHideCountry" => $neverHideCountry,
            "debug" => $this->isDebugging(),
            "translations" => [
                "defaultError" => htmlspecialchars(__('Unknown postcode + housenumber combination.'))
            ]
        ];
    }

    /**
     * Check if we're currently in debug mode, and if the current user may see dev info.
     *
     * @return bool
     */
    public function isDebugging()
    {
        if ($this->debuggingOverride) {
            return true;
        }

        return (bool) $this->getStoreConfig('postcodenl_api/advanced_config/api_debug') &&
            $this->developerHelper->isDevAllowed();
    }

    /**
     * Set the debugging override flag.
     *
     * @param bool $toggle
     */
    protected function setDebuggingOverride($toggle)
    {
        $this->debuggingOverride = $toggle;
    }

    /**
     * Lookup information about a Dutch address by postcode, house number, and house number addition
     *
     * @param string $postcode
     * @param string $houseNumber
     * @param string $houseNumberAddition
     *
     * @return string|array
     */
    public function lookupAddress(string $postcode, string $houseNumber, string $houseNumberAddition)
    {
        // Check if we are we enabled, configured & capable of handling an API request
        $message = $this->checkApiReady();
        if ($message) {
            return $message;
        }

        $response = [];

        // Some basic user data 'fixing', remove any not-letter, not-number characters
        $postcode = preg_replace('~[^a-z0-9]~i', '', $postcode);

        // Basic postcode format checking
        if (!preg_match('~^[1-9][0-9]{3}[a-z]{2}$~i', $postcode)) {
            $response['message'] = __('Invalid postcode format, use `1234AB` format.');
            $response['messageTarget'] = 'postcode';
            return $response;
        }

        $urlEncPostcode = rawurlencode($postcode);
        $urlEncHousenumber = rawurlencode($houseNumber);
        $urlEncHousenumberAdd = rawurlencode($houseNumberAddition);
        $url = "{$this->getServiceUrl()}/nl/v1/addresses/postcode/{$urlEncPostcode}/{$urlEncHousenumber}/{$urlEncHousenumberAdd}";

        $jsonData = $this->callApiUrlGet($url);

        if ($this->getStoreConfig('postcodenl_api/development_config/api_showcase')) {
            $response['showcaseResponse'] = $jsonData;
        }

        if ($this->isDebugging()) {
            $response['debugInfo'] = $this->getDebugInfo($url, $jsonData);
        }

        if ($this->httpResponseCode == 200 && is_array($jsonData) && isset($jsonData['postcode'])) {
            $response = array_merge($response, $jsonData);
        } else {
            $response = $this->processErrorMessage($jsonData, $response);
        }

        return $response;
    }

    protected function processErrorMessage($jsonData, $response)
    {
        if (is_array($jsonData) && isset($jsonData['exceptionId'])) {
            if ($this->httpResponseCode == 400 || $this->httpResponseCode == 404) {
                if (in_array($jsonData['exceptionId'], [
                    'PostcodeNl_Controller_Address_PostcodeTooShortException',
                    'PostcodeNl_Controller_Address_PostcodeTooLongException',
                    'PostcodeNl_Controller_Address_NoPostcodeSpecifiedException',
                    'PostcodeNl_Controller_Address_InvalidPostcodeException',
                ])) {
                    $response['message'] = __('Invalid postcode format, use `1234AB` format.');
                    $response['messageTarget'] = 'postcode';
                } elseif (in_array($jsonData['exceptionId'], [
                    'PostcodeNl_Service_PostcodeAddress_AddressNotFoundException',
                ])) {
                    $response['message'] = __('Unknown postcode + housenumber combination.');
                    $response['messageTarget'] = 'housenumber';
                } elseif (in_array($jsonData['exceptionId'], [
                    'PostcodeNl_Controller_Address_InvalidHouseNumberException',
                    'PostcodeNl_Controller_Address_NoHouseNumberSpecifiedException',
                    'PostcodeNl_Controller_Address_NegativeHouseNumberException',
                    'PostcodeNl_Controller_Address_HouseNumberTooLargeException',
                    'PostcodeNl_Controller_Address_HouseNumberIsNotAnIntegerException',
                ])) {
                    $response['message'] = __('Housenumber format is not valid.');
                    $response['messageTarget'] = 'housenumber';
                } else {
                    $response['message'] = __('Incorrect address.');
                    $response['messageTarget'] = 'housenumber';
                }
            } elseif ($this->httpResponseCode == 401) {
                $response['message'] = __('Postcode service unavailable, please use manual input');
                $response['messageTarget'] = 'housenumber';
            } else {
                $response['message'] = __('Validation error, please use manual input.');
                $response['messageTarget'] = 'housenumber';
                $response['useManual'] = true;
            }
        } else {
            $response['message'] = __('Validation unavailable, please use manual input.');
            $response['messageTarget'] = 'housenumber';
            $response['useManual'] = true;
        }
        return $response;
    }

    /**
     * Set the enrichType number, or text/class description if not in known enrichType list
     *
     * @param mixed $enrichType
     */
    public function setEnrichType($enrichType)
    {
        $this->enrichType = preg_replace('~[^0-9a-z\-_,]~i', '', $enrichType);
        if (strlen($this->enrichType) > 40) {
            $this->enrichType = substr($this->enrichType, 0, 40);
        }
    }

    protected function getDebugInfo($url, $jsonData)
    {
        return [
            'requestUrl' => $url,
            'rawResponse' => $this->httpResponseRaw,
            'responseCode' => $this->httpResponseCode,
            'responseCodeClass' => $this->httpResponseCodeClass,
            'parsedResponse' => $jsonData,
            'httpClientError' => $this->httpClientError,
            'configuration' => [
                'url' => $this->getServiceUrl(),
                'key' => $this->getKey(),
                'secret' => substr($this->getSecret(), 0, 6) . '[hidden]',
                'showcase' => $this->getStoreConfig('postcodenl_api/advanced_config/api_showcase'),
                'debug' => $this->getStoreConfig('postcodenl_api/advanced_config/api_debug'),
            ],
            'magentoVersion' => $this->getMagentoVersion(),
            'extensionVersion' => $this->getExtensionVersion(),
            'modules' => $this->getMagentoModules(),
        ];
    }

    public function testConnection()
    {
        // Default is not OK
        /** @noinspection PhpUnusedLocalVariableInspection */
        $message = __('The test connection could not be successfully completed.');
        $status = 'error';
        $info = [];

        // Do a test address lookup
        $this->setDebuggingOverride(true);
        $addressData = $this->lookupAddress('2012ES', '30', '');
        $this->setDebuggingOverride(false);

        if (!isset($addressData['debugInfo']) && isset($addressData['message'])) {
            // Client-side error
            $message = $addressData['message'];
            if (isset($addressData['info'])) {
                $info = $addressData['info'];
            }
        } else {
            if ($addressData['debugInfo']['httpClientError']) {
                // We have a HTTP connection error
                $message = __('Your server could not connect to the Postcode.nl server.');

                $info = $this->processHttpClientErrorInfo($addressData, $info);
            } else {
                if (!is_array($addressData['debugInfo']['parsedResponse'])) {
                    // We have not received a valid JSON response

                    $message = __('The response from the Postcode.nl service could not be understood.');
                    $info[] = '- ' . __('The service might be temporarily unavailable, if problems persist, ' .
                            'please contact <a href=\'mailto:info@postcode.nl\'>info@postcode.nl</a>.');
                    $info[] = '- ' . __('Technical reason: No valid JSON was returned by the request.');
                } else {
                    if (is_array($addressData['debugInfo']['parsedResponse'])
                        && isset($addressData['debugInfo']['parsedResponse']['exceptionId'])) {
                        // We have an exception message from the service itself

                        if ($addressData['debugInfo']['responseCode'] == 401) {
                            $exceptionId = $addressData['debugInfo']['parsedResponse']['exceptionId'];
                            if ($exceptionId == self::EXCEPTION_NOT_AUTHORIZED) {
                                $message = __('`API Key` specified is incorrect.');
                            } else {
                                if ($exceptionId == self::EXCEPTION_PASSWORD_NOT_CORRECT) {
                                    $message = __('`API Secret` specified is incorrect.');
                                } else {
                                    $message = __('Authentication is incorrect.');
                                }
                            }
                        } else {
                            if ($addressData['debugInfo']['responseCode'] == 403) {
                                $message = __('Access is denied.');
                            } else {
                                $message = __('Service reported an error.');
                            }
                        }
                        $info[] = __('Postcode.nl service message:') . ' "' .
                            $addressData['debugInfo']['parsedResponse']['exception'] . '"';
                    } else {
                        if (is_array($addressData['debugInfo']['parsedResponse'])
                            && !isset($addressData['debugInfo']['parsedResponse']['postcode'])) {
                            // This message is thrown when the JSON returned did not contain the data expected.

                            $message = __('The response from the Postcode.nl service could not be understood.');
                            $info[] = '- ' . __('The service might be temporarily unavailable, if problems persist, ' .
                                'please contact <a href=\'mailto:info@postcode.nl\'>info@postcode.nl</a>.');
                            $info[] = '- ' . __('Technical reason: Received JSON data did not contain expected data.');
                        } else {
                            $message = __('A test connection to the API was successfully completed.');
                            $status = 'success';
                        }
                    }
                }
            }
        }

        return [
            'message' => $message,
            'status' => $status,
            'info' => $info,
        ];
    }

    protected function processHttpClientErrorInfo($addressData, $info)
    {
        // Do some common SSL CA problem detection
        if (strpos(
            $addressData['debugInfo']['httpClientError'],
            'SSL certificate problem, verify that the CA cert is OK'
        ) !== false) {
            $info[] = __('Your servers\' \'cURL SSL CA bundle\' is missing or outdated. Further information:');
            $info[] = '- <a href="https://stackoverflow.com/questions/6400300/https-and-ssl3-get-server-' .
                'certificatecertificate-verify-failed-ca-is-ok" target="_blank">' .
                __('How to update/fix your CA cert bundle') . '</a>';
            $info[] = '- <a href="https://curl.haxx.se/docs/sslcerts.html" target="_blank">' .
                __('About cURL SSL CA certificates') . '</a>';
            $info[] = '';
        } else {
            if (strpos(
                $addressData['debugInfo']['httpClientError'],
                'unable to get local issuer certificate'
            ) !== false) {
                $info[] = __('cURL cannot read/access the CA cert file:');
                $info[] = '- <a href="https://curl.haxx.se/docs/sslcerts.html" target="_blank">' .
                    __('About cURL SSL CA certificates') . '</a>';
                $info[] = '';
            } else {
                $info[] = __('Connection error.');
            }
        }
        $info[] = __('Error message:') . ' "' . $addressData['debugInfo']['httpClientError'] . '"';
        $info[] = '- <a href="https://www.google.com/search?q=' .
            urlencode($addressData['debugInfo']['httpClientError']) .
            '" target="_blank">' . __('Google the error message') . '</a>';
        $info[] = '- ' . __('Contact your hosting provider if problems persist.');
        return $info;
    }

    protected function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    protected function getKey()
    {
        return trim($this->getStoreConfig('postcodenl_api/general/api_key'));
    }

    protected function getSecret()
    {
        return trim($this->getStoreConfig('postcodenl_api/general/api_secret'));
    }

    protected function getServiceUrl()
    {
        $serviceUrl = trim($this->getStoreConfig('postcodenl_api/development_config/api_url'));
        if (empty($serviceUrl)) {
            $serviceUrl = self::API_URL;
        }

        return $serviceUrl;
    }

    protected function getMagentoVersion()
    {
        if ($this->getModuleInfo('Enterprise_CatalogPermissions') !== null) {
            // Detect enterprise
            return 'MagentoEnterprise/' . $this->productMetadataInterface->getVersion();
        } elseif ($this->getModuleInfo('Enterprise_Enterprise') !== null) {
            // Detect professional
            return 'MagentoProfessional/' . $this->productMetadataInterface->getVersion();
        }

        return 'Magento/' . $this->productMetadataInterface->getVersion();
    }

    protected function getModuleInfo($moduleName)
    {
        $modules = $this->getMagentoModules();

        if (!isset($modules[$moduleName])) {
            return null;
        }

        return $modules[$moduleName];
    }

    protected function getConfigBoolString($configKey)
    {
        if ($this->getStoreConfig($configKey)) {
            return true;
        }

        return false;
    }

    protected function curlHasSsl()
    {
        $curlVersion = curl_version();
        return $curlVersion['features'] & CURL_VERSION_SSL;
    }

    protected function checkApiReady()
    {
        if (!$this->debuggingOverride
            && !(
                $this->getStoreConfig('postcodenl_api/general/enabled')
                || $this->getStoreConfig('postcodenl_api/advanced_config/admin_validation_enabled')
            )
        ) {
            return ['message' => __('Postcode.nl API not enabled.')];
        }

        if ($this->getServiceUrl() === '' || $this->getKey() === '' || $this->getSecret() === '') {
            return [
                'message' => __('Postcode.nl API not configured.'),
                'info' => [__('Configure your `API key` and `API secret`.')]
            ];
        }

        return $this->checkCapabilities();
    }

    protected function checkCapabilities()
    {
        // Check for SSL support in CURL
        if (!$this->curlHasSsl()) {
            return [
                'message' => __('Cannot connect to Postcode.nl API: Server is missing SSL (https) support for CURL.')
            ];
        }

        return false;
    }

    protected function callApiUrlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::API_TIMEOUT);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->getKey() . ':' . $this->getSecret());
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());

        $this->httpResponseRaw = curl_exec($ch);
        $this->httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->httpResponseCodeClass = (int) floor($this->httpResponseCode / 100) * 100;
        $curlErrno = curl_errno($ch);
        $this->httpClientError = $curlErrno ? sprintf('cURL error %s: %s', $curlErrno, curl_error($ch)) : null;

        curl_close($ch);

        return json_decode($this->httpResponseRaw, true);
    }

    protected function getExtensionVersion()
    {
        $extensionInfo = $this->getModuleInfo('PostcodeNl_Api');
        return $extensionInfo ? (string) $extensionInfo['version'] : 'unknown';
    }

    protected function getUserAgent()
    {
        return 'PostcodeNl_Api_MagentoPlugin/' . $this->getExtensionVersion() . ' ' .
            $this->getMagentoVersion() . ' PHP/' . phpversion() . ' EnrichType/' . $this->enrichType;
    }

    protected function getMagentoModules()
    {
        if ($this->modules !== null) {
            return $this->modules;
        }

        $this->modules = [];

        foreach ($this->moduleList->getAll() as $name => $module) {
            $this->modules[$name] = [];
            foreach ($module as $key => $value) {
                if (in_array((string) $key, ['setup_version', 'name'])) {
                    $this->modules[$name][$key] = (string) $value;
                }
            }
        }

        return $this->modules;
    }
}
