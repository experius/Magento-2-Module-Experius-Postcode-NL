<?php
/**
 * A Magento 2 module named Experius Postcode
 * Copyright (C) 2017 Experius
 *
 * This file is part of Experius Postcode.
 *
 * Experius Postcode is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Experius\Postcode\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\View\Element\AbstractBlock;

class LayoutProcessor extends AbstractBlock implements LayoutProcessorInterface
{
    protected $scopeConfig;

    protected $logger;

    protected $changeFieldPositions = false;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->logger = $context->getLogger();

        parent::__construct($context, $data);
    }

    /**
     * Process js Layout of block
     *
     * @param array $result ($jsLayout)
     * @return array
     */
    public function process($result)
    {
        if (
            $this->scopeConfig->getValue(
                'postcodenl_api/general/enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            && isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset'])
        ) {
            $this->changeFieldPositions = $this->scopeConfig->getValue(
                'postcodenl_api/advanced_config/change_sort_order',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $shippingFields = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];

            $shippingFields = array_merge($shippingFields, $this->getPostcodeFieldSet('shippingAddress', 'shipping'));

            if ($this->changeFieldPositions) {
                $shippingFields = $this->changeAddressFieldPosition($shippingFields);
            }

            $shippingFields = $this->addClasses('shippingAddress', $shippingFields);

            $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'] = $shippingFields;

            $result = $this->getBillingFormFields($result);
        }
        return $result;
    }

    /**
     * Get billing form fields
     *
     * @param array $result
     * @return array
     */
    public function getBillingFormFields($result)
    {
        if (isset(
            $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['payments-list']
        )) {
            $paymentForms = $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];

            foreach (array_keys($paymentForms) as $paymentMethodForm) {
                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);

                if (!isset($result['components']['checkout']['children']['steps']['children']['billing-step']
                    ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }

                $billingFields = $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];

                $billingPostcodeFields = $this->getPostcodeFieldSet('billingAddress' . $paymentMethodCode, 'billing');

                $billingFields = array_merge($billingFields, $billingPostcodeFields);

                $billingFields = $this->addClasses('billingAddress' . $paymentMethodCode, $billingFields);

                if ($this->changeFieldPositions) {
                    $billingFields = $this->changeAddressFieldPosition($billingFields);
                }

                $result['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form']
                ['children']['form-fields']['children'] = $billingFields;
            }
        }

        return $result;
    }

    /**
     * Get postcode fieldset
     *
     * @param $scope
     * @param $addressType
     * @return array[]
     */
    public function getPostcodeFieldSet($scope, $addressType)
    {
        return [
            'experius_postcode_fieldset' => [
                'component' => 'Experius_Postcode/js/view/form/postcode',
                'type' => 'group',
                'config' => [
                    "customScope" => $scope,
                    "template" => 'Experius_Postcode/form/group',
                    "additionalClasses" => "experius_postcode_fieldset",
                    "loaderImageHref" => $this->getViewFileUrl('images/loader-1.gif')
                ],
                'sortOrder' => (string)($this->getStartSortOrder() + 5),
                'children' => $this->getPostcodeFields($scope, $addressType),
                'provider' => 'checkoutProvider',
                'addressType' => $addressType,
            ]
        ];
    }

    /**
     * Get postcode fields
     *
     * @param $scope
     * @param $addressType
     * @return array[]
     */
    public function getPostcodeFields($scope, $addressType)
    {
        $postcodeFields =
            [
                'experius_postcode_postcode' => [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        "customScope" => $scope,
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'ui/form/element/input',
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => $scope . '.experius_postcode_postcode',
                    'label' => __('Postcode'),
                    'sortOrder' => (string)($this->getStartSortOrder() + 15),
                    'validation' => [
                        'required-entry' => true,
                        'min_text_length' => 6,
                    ]
                ],
                'experius_postcode_housenumber' => [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        "customScope" => $scope,
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'Experius_Postcode/form/element/housenumber'
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => $scope . '.experius_postcode_housenumber',
                    'label' => __('Housenr.'),
                    'sortOrder' => (string)($this->getStartSortOrder() + 25),
                    'validation' => [
                        'required-entry' => true,
                    ],
                ],
                'experius_postcode_housenumber_addition' => [
                    'component' => 'Magento_Ui/js/form/element/select',
                    'config' => [
                        "customScope" => $scope,
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'ui/form/element/select'
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => $scope . '.experius_postcode_housenumber_addition',
                    'label' => __('Addition'),
                    'sortOrder' => (string)($this->getStartSortOrder() + 30),
                    'validation' => [
                        'required-entry' => false,
                    ],
                    'options' => [],
                    'visible' => false,
                ],
                'experius_postcode_housenumber_addition_manual' => [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        "customScope" => $scope,
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'ui/form/element/input'
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => $scope . '.experius_postcode_housenumber_addition_manual',
                    'label' => __('Addition'),
                    'sortOrder' => (string)($this->getStartSortOrder() + 30),
                    'validation' => [
                        'required-entry' => false,
                    ],
                    'options' => [],
                    'visible' => false,
                ],
                'experius_postcode_disable' => [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        "customScope" => $scope,
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'ui/form/element/checkbox'
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => $scope . '.experius_postcode_disable',
                    'description' => __('Enter address manually'),
                    'sortOrder' => (string)($this->getStartSortOrder() + 50),
                    'validation' => [
                        'required-entry' => false,
                    ],
                    'addressType' => $addressType
                ]
            ];

        return $postcodeFields;
    }

    /**
     * Change address fields position(s)
     *
     * @param $addressFields
     * @return mixed
     */
    public function changeAddressFieldPosition($addressFields)
    {
        if (isset($addressFields['country_id'])) {
            $addressFields['country_id']['sortOrder'] = (string)($this->getStartSortOrder());
        }

        // 'experius_postcode_fieldset' is $this->getStartSortOrder() + 5

        if (isset($addressFields['street'])) {
            $addressFields['street']['sortOrder'] = (string)($this->getStartSortOrder() + 10);
        }

        // 'experius_postcode_postcode' field default is + 15

        if (isset($addressFields['city'])) {
            $addressFields['city']['sortOrder'] = (string)($this->getStartSortOrder() + 20);
        }

        // 'experius_postcode_housenumber' is + 25
        // 'experius_postcode_housenumber_addition' and 'experius_postcode_housenumber_addition_manual' are + 30

        if (isset($addressFields['postcode'])) {
            $addressFields['postcode']['sortOrder'] = (string)($this->getStartSortOrder() + 35);
        }

        if (isset($addressFields['region'])) {
            $addressFields['region']['sortOrder'] = (string)($this->getStartSortOrder() + 40);
        }

        if (isset($addressFields['region_id'])) {
            $addressFields['region_id']['sortOrder'] = (string)($this->getStartSortOrder() + 45);
        }

        // 'experius_postcode_disable' is + 50

        return $addressFields;
    }

    /**
     * Get start sort order
     * Hook for plugins to alter it's default: 900
     *
     * @TODO: Introduce system configuration to alter this value
     * @return int
     */
    public function getStartSortOrder()
    {
        return 900;
    }

    /**
     * Add classes to address fields
     *
     * @param $scope
     * @param $addressFields
     * @return mixed
     */
    public function addClasses($scope, $addressFields)
    {
        foreach (['street', 'region_id', 'region', 'country_id', 'city', 'postcode'] as $field) {
            if (isset($addressFields[$field])) {
                $configAdditionalClasses = null;
                $additionalClasses = $scope . '-' . $field;
                if (isset($addressFields[$field]['config']['additionalClasses'])) {
                    $configAdditionalClasses = $addressFields[$field]['config']['additionalClasses'];
                    $additionalClasses = $configAdditionalClasses . ' ' . $additionalClasses;
                }
                $addressFields[$field]['config']['additionalClasses'] = $additionalClasses;
            }
        }

        return $addressFields;
    }
}
