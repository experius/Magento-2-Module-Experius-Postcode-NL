<?php 


namespace Experius\Postcode\Plugin\Magento\Checkout\Block\Checkout;
 
 
class LayoutProcessor {
    
    protected $scopeConfig;
	
	protected $logger;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Psr\Log\LoggerInterface $logger
    ){
        $this->scopeConfig = $scopeConfig;
		$this->logger = $logger;
    }


	public function afterProcess(
		\Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
		$result
	){
       
        if($this->scopeConfig->getValue('postcodenl_api/config/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
			
			$shippingPostcodeFields = $this->getPostcodeFields('shippingAddress');
            
			$shippingFields = $result['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['shipping-address-fieldset']['children'];
			
			$shippingFields = array_merge($shippingFields,$shippingPostcodeFields);
			
			$result['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['shipping-address-fieldset']['children'] = $shippingFields;
						 					 
			$result = $this->getBillingFormFields($result);

        }
		
        return $result;
	}
	
	public function getBillingFormFields($result){
		

        $paymentForms = $result['components']['checkout']['children']['steps']['children']
					 ['billing-step']['children']['payment']['children']
						 ['payments-list']['children'];
		
		foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {
			
			$paymentMethodCode = str_replace('-form','',$paymentMethodForm);
			
			if(!isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$paymentMethodCode.'-form'])){
				continue;
			}
			
			$billingFields = $result['components']['checkout']['children']['steps']['children']
					 ['billing-step']['children']['payment']['children']
						 ['payments-list']['children'][$paymentMethodCode.'-form']['children']['form-fields']['children'];
			
			$billingPostcodeFields = $this->getPostcodeFields('billingAddress'.$paymentMethodCode);
			
			$billingFields = array_merge($billingFields,$billingPostcodeFields); 
			
			$result['components']['checkout']['children']['steps']['children']
					 ['billing-step']['children']['payment']['children']
						 ['payments-list']['children'][$paymentMethodCode.'-form']['children']['form-fields']['children'] = $billingFields;
			
		}
		
		return $result;

	}
	
	public function getPostcodeFields($scope){
		$postcodeTitle = __('Postcode');
        $housnumberTitle = __('Housenumber');
        
		$postcodeFields =    
		[
		 'experius_postcode_postcode'=>
			[
				'component' => 'Experius_Postcode/js/view/form/element/postcode',
				'config' => [
					"customerScope" => $scope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/input',
					//"tooltip" => [
					//    "description" => $postcodeToolTip
					//]
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_postcode',
				'label' => $postcodeTitle,
				'sortOrder' => '1000',
				'validation' => [
					'required-entry' => $this->scopeConfig->getValue('experius_ponumber/general/required',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
				],
			]
		, 'experius_postcode_housenumber'=>
			[
				'component' => 'Experius_Postcode/js/view/form/element/postcode',
				'config' => [
					"customerScope" => $scope,
					"template" => 'Experius_Postcode/form/field',
					"elementTmpl" => 'ui/form/element/input',
					//"tooltip" => [
					//    "description" => $postcodeToolTip
					//]
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_housenumber',
				'label' => $housnumberTitle,
				'sortOrder' => '1001',
				'validation' => [
					'required-entry' => $this->scopeConfig->getValue('experius_ponumber/general/required',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
				],
			]
		];
		
		return $postcodeFields;
	}
	
	
	
}