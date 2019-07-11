<?php

namespace Experius\Postcode\Controller\Adminhtml\System\Config;

class Apicheck extends \Magento\Framework\App\Action\Action
{
    const API_URL = 'https://api.postcode.eu';
    const API_TIMEOUT = 3;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Apicheck constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $newApiKey = $params['apikey'];
        $newApiSecret = $params['apisecret'];
        $access = json_decode($this->checkApiKey($newApiKey, $newApiSecret));
        if (property_exists($access, 'exception')) {
            $result['message'] = __('Key or secret are invalid');
            $result['key_is_valid'] = 'no';
        } else {
            if ($access->hasAccess == 1) {
                $result['message'] = __("Key is valid and has access");
                $result['key_is_valid'] = 'yes';
            } else {
                $result['message'] = __("Key is valid but has no access");
                $result['key_is_valid'] = 'no';
            }
        }

        return $this->resultJsonFactory->create()->setData($result);
    }

    private function checkApiKey($newApiKey, $newApiSecret)
    {
        $url = self::API_URL . "/account/v1/info";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::API_TIMEOUT);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $newApiKey . ':' . $newApiSecret);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
