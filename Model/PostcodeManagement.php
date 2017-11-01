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

namespace Experius\Postcode\Model;

/**
 * Class PostcodeManagement
 * @package Experius\Postcode\Model
 */
class PostcodeManagement
{

    /**
     * @var \Experius\Postcode\Helper\Data
     */
    protected $postcodeHelper;

    /**
     * PostcodeManagement constructor.
     * @param \Experius\Postcode\Helper\Data $postcodeHelper Postcode helper.
     */
    public function __construct(
        \Experius\Postcode\Helper\Data $postcodeHelper
    ) {
        $this->postcodeHelper = $postcodeHelper;
    }

    /**
     * @param string $postcode            The postcode you would like to get information for.
     * @param string $houseNumber         The housenumber you would like to get information for.
     * @param string $houseNumberAddition The housenumber addition you would like to get information for.
     * @return string
     */
    public function getPostcodeInformation(string $postcode, string $houseNumber, string $houseNumberAddition)
    {
        $result = $this->postcodeHelper->lookupAddress($postcode, $houseNumber, $houseNumberAddition);
        return json_encode($result);
    }
}
