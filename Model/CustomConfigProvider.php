<?php

namespace Experius\Postcode\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class CustomConfigProvider implements ConfigProviderInterface
{
    
    protected $postcodeHelper;
    
    public function __construct(
        \Experius\Postcode\Helper\Data $postcodeHelper
    ){
        $this->postcodeHelper = $postcodeHelper;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {        
        $config = [
            'experius_postcode' => [
                'settings' => $this->postcodeHelper->getVatRegexArray()
            ]
        ];
        return $config;
    }
}
