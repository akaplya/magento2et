<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogEt\Model\Product;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\ColumnValueExpression;

/**
 * Class MainEntity
 */
class ProductCategories
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
    public function get(array $values) : array
    {
        $ids = [];
        foreach ($values as $value) {
            $ids[] = $value['product_id'];
        }
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['ccp' => $this->resourceConnection->getTableName('catalog_category_product')],
                [
                    'product_id' => 'ccp.product_id',
                ]
            )
            ->join(
                ['cce' => $this->resourceConnection->getTableName('catalog_category_entity')],
                'cce.entity_id = ccp.category_id',
                []
            )
            ->join(
                ['cpath' => $this->resourceConnection->getTableName('catalog_category_entity')],
                "find_in_set(cpath.entity_id, replace(cce.path, '/', ','))",
                []
            )
            ->join(
                ['ccev' => $this->resourceConnection->getTableName('catalog_category_entity_varchar')],
                'cpath.entity_id = ccev.entity_id and attribute_id = 119 and store_id = 0',
                []
            )
            ->group(['product_id', 'category_id'])
            ->where('ccp.product_id IN (?)', $ids)
            ->columns(
                [
                    'categories' => new ColumnValueExpression("group_concat(ccev.value order by cpath.level separator  '/' )")
                ]
            );

        return $connection->fetchAll($select);
    }
}
