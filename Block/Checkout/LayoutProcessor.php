<?php 


namespace Experius\Postcode\Block\Checkout;
 
 
class LayoutProcessor extends \Magento\Framework\View\Element\AbstractBlock implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface{
    
    protected $scopeConfig;
	
	protected $logger;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ){
        $this->scopeConfig = $scopeConfig;
		$this->logger = $logger;

        parent::__construct($context, $data);
    }

	public function process($result){
       
        if($this->scopeConfig->getValue('postcodenl_api/general/enabled',\Magento\Store\Model\ScopeInterface::SCOPE_STORE) &&
            isset($result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'])
        ){
			
			$shippingPostcodeFields = $this->getPostcodeFields('shippingAddress','shipping');
            
			$shippingFields = $result['components']['checkout']['children']['steps']['children']
					 ['shipping-step']['children']['shippingAddress']['children']
						 ['shipping-address-fieldset']['children'];

            if(isset($shippingFields['street'])){
                unset($shippingFields['street']['children'][1]['validation']);
                unset($shippingFields['street']['children'][2]['validation']);
            }

            $shippingFields = array_merge($shippingFields,$this->getPostcodeFieldSet('shippingAddress'));

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

                $billingPostcodeFields = $this->getPostcodeFields('billingAddress' . $paymentMethodCode,'billing');

                $billingFields = array_merge($billingFields, $billingPostcodeFields);

                $result['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'] = $billingFields;

            }
        }
		
		return $result;

	}

	public function getPostcodeFieldSet($scope){
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
                    'children' => $this->getPostcodeFields($scope,'shipping')
                ]
        ];
    }
	
	public function getPostcodeFields($scope,$addressType='shipping'){
		
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
				'sortOrder' => '1000',
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
				'sortOrder' => '1001',
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
				'sortOrder' => '1002',
				'validation' => [
					'required-entry' => false,
				],
				'options' => [],
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
				'label' => __('Enter address manually'),
				'sortOrder' => '1004',
				'validation' => [
					'required-entry' => false,
				],
                'addressType'=> $addressType
			]
		];
		
		return $postcodeFields;
	}
}