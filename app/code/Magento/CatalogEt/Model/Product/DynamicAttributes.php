<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogEt\Model\Product;

use Magento\Framework\App\ResourceConnection;

/**
 * Class DynamicAttributes
 */
class DynamicAttributes
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * DynamicAttributes constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $values
     * @return array
     */
    public function get(array $values) : array
    {
        $ids = [];
        foreach ($values as $value) {
            $ids[] = $value['product_id'];
        }
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['t' => $this->resourceConnection->getTableName('catalog_product_entity')],
                []
            )
            ->joinLeft(
                ['v' => $this->resourceConnection->getTableName('catalog_product_entity_varchar')],
                'v.entity_id = t.entity_id AND v.store_id = 0',
                []
            )
            ->joinLeft(
                ['a' => $this->resourceConnection->getTableName('eav_attribute')],
                'a.attribute_id = v.attribute_id',
                []
            )
            ->where('t.entity_id IN (?) ', $ids)
            ->columns(
                [
                    'product_id' => 't.entity_id',
                    'attribute_code' => 'a.attribute_code',
                    'value' => 'v.value'
                ]
            );
        return $connection->fetchAll($select);
    }
}
