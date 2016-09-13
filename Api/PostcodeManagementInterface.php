<?php

namespace Experius\Postcode\Api;

interface PostcodeManagementInterface
{
    
    /**
    * Set a ponumber on the cart
    *
    * @param string $postcode.
    * @param string $houseNumber
    * @param string $houseNumberAddition
    * @return string
    */
    
    public function getPostcodeInformation($postcode,$houseNumber,$houseNumberAddition);
    
}
