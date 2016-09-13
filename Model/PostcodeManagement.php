<?php

namespace Experius\Postcode\Model;

class PostcodeManagement {
    
    protected $postcodeHelper;
    
    public function __construct(
        \Experius\Postcode\Helper\Data $postcodeHelper
    ){
        $this->postcodeHelper = $postcodeHelper;
    }
   
    /**
     * {@inheritdoc}
    */
    public function getPostcodeInformation($postcode,$houseNumber,$houseNumberAddition){
        $result = $this->postcodeHelper->lookupAddress($postcode, $houseNumber, $houseNumberAddition);
        return json_encode($result);
    }
    
}