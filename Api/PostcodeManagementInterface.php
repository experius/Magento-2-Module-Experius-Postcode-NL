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

namespace Experius\Postcode\Api;

interface PostcodeManagementInterface
{

    /**
     * Set a ponumber on the cart
     *
     * @param  string $postcode .
     * @param  string $houseNumber
     * @param  string $houseNumberAddition
     * @return string
     */

    public function getPostcodeInformation(string $postcode, string $houseNumber, string $houseNumberAddition);
}
