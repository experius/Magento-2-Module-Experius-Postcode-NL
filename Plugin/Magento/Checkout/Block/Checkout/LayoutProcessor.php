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
       
        if($this->scopeConfig->getValue('experius_ponumber/general/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
			
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
				'label' => __('Postcode'),
				'sortOrder' => '1000',
				'validation' => [
					'required-entry' => true,
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
					//    "description" => $ponumberToolTip
					//]
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_housenumber',
				'label' => __('Housenumber'),
				'sortOrder' => '1001',
				'validation' => [
					'required-entry' => true,
				],
			],
			'experius_postcode_housenumber_addition'=>
			[
				'component' => 'Magento_Ui/js/form/element/select',
				'config' => [
					"customerScope" => $scope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/select',
					//"tooltip" => [
					//    "description" => $ponumberToolTip
					//]
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_housenumber_addition',
				'label' => __('Addition'),
				'sortOrder' => '1002',
				'validation' => [
					'required-entry' => false,
				],
				'options' => ['0'=>['value'=>'test','label'=>'test'],'1'=>['value'=>'test2','label'=>'test2']],
				'visible' => false
			],
			'experius_postcode_disable'=>
			[
				'component' => 'Experius_Postcode/js/view/form/element/postcode',
				'config' => [
					"customerScope" => $scope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/checkbox',
					//"tooltip" => [
					//    "description" => $ponumberToolTip
					//]
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_disable',
				'label' => __('Enter address manually'),
				'sortOrder' => '1004',
				'validation' => [
					'required-entry' => false,
				],
			]
		];
		
		return $postcodeFields;
	}
	
	
	
}