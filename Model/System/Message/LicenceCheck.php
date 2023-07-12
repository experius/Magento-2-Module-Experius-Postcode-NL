<?php

namespace Experius\Postcode\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;

class LicenceCheck implements MessageInterface
{
    const MESSAGE_IDENTITY = 'experius_system_message';
    
    /**
     * @var \Experius\Postcode\Helper\Data 
     */
    protected $postcodeHelper;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $scopeConfig;

    public function __construct(
        \Experius\Postcode\Helper\Data $postcodeHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->postcodeHelper = $postcodeHelper;
        $this->scopeConfig = $scopeConfig;
    }

    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    public function isDisplayed()
    {
        $keyIsValid = $this->scopeConfig->getValue('postcodenl_api/general/api_key_is_valid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return !($keyIsValid == 'yes');
    }

    public function getText()
    {
        return __('Your Postcode.nl API licence is invalid');
    }

    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}
