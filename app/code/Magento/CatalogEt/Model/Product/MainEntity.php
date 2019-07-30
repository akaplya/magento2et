<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogEt\Model\Product;

use Magento\Framework\App\ResourceConnection;

/**
 * Class MainEntity
 */
class MainEntity
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * MainEntity constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $ids
     * @return array
     */
    public function get(array $ids = [1]) : array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['t' => $this->resourceConnection->getTableName('catalog_product_entity')],
                [
                    'product_id' => 't.entity_id',
                    'sku' => 't.sku'
                ])
            ->joinLeft(
                ['a_name' => $this->resourceConnection->getTableName('catalog_product_entity_varchar')],
                'a_name.entity_id = t.entity_id AND store_id = 0 AND attribute_id = 73',
                ['name' => 'a_name.value']
            )
        ;
        return $connection->fetchAll($select->limit(1000));
    }
}
