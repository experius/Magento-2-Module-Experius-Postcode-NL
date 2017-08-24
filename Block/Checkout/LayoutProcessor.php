<?php 


namespace Experius\Postcode\Block\Checkout;
 
 
class LayoutProcessor extends \Magento\Framework\View\Element\AbstractBlock implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface{
    
    protected $scopeConfig;
	
	protected $logger;

	protected $changeFieldPositions = false;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ){
        $this->scopeConfig = $context->getScopeConfig();
		$this->logger = $context->getLogger();

        parent::__construct($context, $data);
    }

	public function process($result){
       
        if($this->scopeConfig->getValue('postcodenl_api/general/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE) &&
            isset($result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'])
        ){
            $this->changeFieldPositions = $this->scopeConfig->getValue('postcodenl_api/advanced_config/change_sort_order',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

			$shippingFields = $result['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['shipping-address-fieldset']['children'];

            if(isset($shippingFields['street'])){
                unset($shippingFields['street']['children'][1]['validation']);
                unset($shippingFields['street']['children'][2]['validation']);
            }

            $shippingFields = array_merge($shippingFields,$this->getPostcodeFieldSet('shippingAddress','shipping'));

            if($this->changeFieldPositions){
                $shippingFields = $this->changeAddressFieldPosition($shippingFields);
            }

            $shippingFields = $this->addClasses('shippingAddress',$shippingFields);

			$result['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['shipping-address-fieldset']['children'] = $shippingFields;
						 					 
			$result = $this->getBillingFormFields($result);

        }
		
        return $result;
	}
	
	public function getBillingFormFields($result){

        if(isset($result['components']['checkout']['children']['steps']['children']
        ['billing-step']['children']['payment']['children']
        ['payments-list'])) {

            $paymentForms = $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];

            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {

                $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);

                if (!isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }

                $billingFields = $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];

                $billingPostcodeFields = $this->getPostcodeFieldSet('billingAddress' . $paymentMethodCode,'billing');

                $billingFields = array_merge($billingFields, $billingPostcodeFields);

                $billingFields = $this->addClasses('billingAddress' . $paymentMethodCode,$billingFields);

                if($this->changeFieldPositions){
                    $billingFields = $this->changeAddressFieldPosition($billingFields);
                }

                $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'] = $billingFields;

            }
        }
		
		return $result;

	}

	public function getPostcodeFieldSet($scope,$addressType){
        return [
            'experius_postcode_fieldset'=>
                [
                    'component' => 'Experius_Postcode/js/view/form/postcode',
                    'type' => 'group',
                    'config' => [
                        "customerScope" => $scope,
                        "template" => 'Experius_Postcode/form/group',
                        "additionalClasses" => "experius_postcode_fieldset",
                        "loaderImageHref" => $this->getViewFileUrl('images/loader-1.gif')
                    ],
                    'children' => $this->getPostcodeFields($scope,$addressType),
                    'provider' => 'checkoutProvider',
                    'addressType'=> $addressType
                ]
        ];
    }
	
	public function getPostcodeFields($scope,$addressType){
		
		$postcodeFields =    
		[
		    'experius_postcode_postcode'=>
			[
				'component' => 'Magento_Ui/js/form/element/abstract',
				'config' => [
					"customerScope" => $scope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/input',
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_postcode',
				'label' => __('Postcode'),
				'sortOrder' => '915',
				'validation' => [
					'required-entry' => 1,
				],
                'addressType'=> $addressType
			],
            'experius_postcode_housenumber'=>
			[
				'component' => 'Magento_Ui/js/form/element/abstract',
				'config' => [
					"customerScope" => $scope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/input'
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_housenumber',
				'label' => __('Housenumber'),
				'sortOrder' => '925',
				'validation' => [
					'required-entry' => 1,
				],
                'addressType'=> $addressType
			],
			'experius_postcode_housenumber_addition'=>
			[
				'component' => 'Magento_Ui/js/form/element/select',
				'config' => [
					"customerScope" => $scope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/select'
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_housenumber_addition',
				'label' => __('Addition'),
				'sortOrder' => '927',
				'validation' => [
					'required-entry' => false,
				],
                'options' => [
                    [
                        'value' => '',
                        'label' => __('No housenumber addition'),
                    ]
                ],
				'visible' => false,
                'addressType'=> $addressType
			],
			'experius_postcode_disable'=>
			[
				'component' => 'Magento_Ui/js/form/element/abstract',
				'config' => [
					"customerScope" => $scope,
					"template" => 'ui/form/field',
					"elementTmpl" => 'ui/form/element/checkbox'
				],
				'provider' => 'checkoutProvider',
				'dataScope' => $scope . '.experius_postcode_disable',
				'description' => __('Enter address manually'),
				'sortOrder' => '1004',
				'validation' => [
					'required-entry' => false,
				],
                'addressType'=> $addressType
			]
		];
		
		return $postcodeFields;
	}

	public function changeAddressFieldPosition($addressFields){

	    if(isset($addressFields['street'])){
            $addressFields['street']['sortOrder'] = '910';
        }

        if(isset($addressFields['postcode'])){
            $addressFields['postcode']['sortOrder'] = '930';
        }

        if(isset($addressFields['city'])){
            $addressFields['city']['sortOrder'] = '920';
        }

        if(isset($addressFields['region'])){
            $addressFields['region']['sortOrder'] = '940';
        }

        if(isset($addressFields['region_id'])){
            $addressFields['region_id']['sortOrder'] = '945';
        }

        if(isset($addressFields['country_id'])){
            $addressFields['country_id']['sortOrder'] = '900';
        }

        return $addressFields;

    }

    public function addClasses($scope,$addressFields){

	    foreach(['street','region_id','region','country_id','city','postcode'] as $field){
            if(isset($addressFields[$field])){
                $addressFields[$field]['config']['additionalClasses'] = (isset($addressFields[$field]['config']['additionalClasses'])) ? $addressFields[$field]['config']['additionalClasses'] . ' ' . $scope . '-'. $field : $scope . '-' . $field;
            }
        }

        return $addressFields;
    }
}
