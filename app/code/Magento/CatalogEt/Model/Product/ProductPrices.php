<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogEt\Model\Product;


class ProductPrices
{
    /**
     * @param array $values
     * @return array
     */
    public function get(array $values) : array
    {
        $result = [];
        foreach ($values as $value) {
            $result[$value['product_id']] =
                [
                    'product_id' => $value['product_id'],
                    'minimalPrice' => [
                        'regular_price' => 1,
                        'sale_price' => 1
                    ],
                    'maximalPrice' => [
                        'regular_price' => 1,
                        'sale_price' => 1
                    ]
                ];
        }
        return $result;
    }
}