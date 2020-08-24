<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\Postcode\Model\Resolver\Postcode;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Identity for resolved CMS page
 */
class Identity implements IdentityInterface
{
    /** @var string */
    private $cacheTag = 'experius_postcode';

    /**
     * Get page ID from resolved data
     *
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {

        $ids =  empty($resolvedData['id']) ?
            [] : [$this->cacheTag, sprintf('%s_%s', $this->cacheTag, $resolvedData['id'])];

        return $ids;
    }
}
