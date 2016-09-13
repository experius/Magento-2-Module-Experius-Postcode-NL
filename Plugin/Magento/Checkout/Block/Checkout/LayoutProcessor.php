<?php 


namespace Experius\Postcode\Plugin\Magento\Checkout\Block\Checkout;
 
 
class LayoutProcessor {
    
    protected $scopeConfig;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->scopeConfig = $scopeConfig;
    }


	public function afterProcess(
		\Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
		$result
	){
        
        if($this->scopeConfig->getValue('experius_ponumber/general/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
			
			$postcodeTitle = __('Postcode');
            $housnumberTitle = __('Housenumber');
        
            $postcodeFields =    
            [
			 'experius_postcode_postcode'=>
                [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        "customerScope" => 'experiusPostcodeForm',
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'ui/form/element/input',
                        //"tooltip" => [
                        //    "description" => $ponumberToolTip
                        //]
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => 'experiusPostcodeForm.experius_postcode_postcode',
                    'label' => $postcodeTitle,
                    'sortOrder' => '10',
                    'validation' => [
                        'required-entry' => $this->scopeConfig->getValue('experius_ponumber/general/required',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    ],
                ]
			, 'experius_postcode_housenumber'=>
                [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        "customerScope" => 'experiusPostcodeForm',
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'ui/form/element/input',
                        //"tooltip" => [
                        //    "description" => $ponumberToolTip
                        //]
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => 'experiusPostcodeForm.experius_postcode_housenumber',
                    'label' => $housnumberTitle,
                    'sortOrder' => '20',
                    'validation' => [
                        'required-entry' => $this->scopeConfig->getValue('experius_ponumber/general/required',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    ],
                ]
            ];
            
            $postcodeForm =
                [
                 'component'=>'Experius_Postcode/js/view/form/postcode',
                 'provider' => 'checkoutProvider',
                 'config'=> [
                    'template'=>'Experius_Postcode/form'
                 ],
                 'children' =>[
                    'experius-postcode-form-fieldset'=>[
                        'component'=>'uiComponent',
                        'displayArea'=>'postcode-checkout-form-fields',
                        'children'=> $postcodeFields
                    ]
                 ]
                    
                ];
            
			$result['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['before-form']['children']['experius-postcode-form-container'] = $postcodeForm;
        }
        
        return $result;
	}
}