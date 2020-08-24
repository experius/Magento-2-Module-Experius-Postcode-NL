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

namespace Experius\Postcode\Model\Resolver;

use Experius\Postcode\Helper\Data as HelperData;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Postcode implements ResolverInterface
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
        HelperData $postcodeHelper
    ) {
        $this->postcodeHelper = $postcodeHelper;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['postcode']) || !$args['postcode']) {
            throw new GraphQlInputException(__('"postcode" should be specified'));
        }
        if (!isset($args['houseNumber']) || !$args['houseNumber']) {
            throw new GraphQlInputException(__('"houseNumber" should be specified'));
        }
        if (!isset($args['houseNumberAddition']) || !$args['houseNumberAddition']) {
            $args['houseNumberAddition'] = '';
        }

        $result = $this->postcodeHelper->lookupAddress($args['postcode'], $args['houseNumber'], $args['houseNumberAddition']);
        if (isset($result['message'])) {
            throw new LocalizedException(__($result['message']));
        }
        return $result;
    }
}
