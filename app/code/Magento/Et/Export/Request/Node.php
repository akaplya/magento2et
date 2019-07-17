<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Export\Request;

/**
 * Class Node
 */
class Node
{
    /**
     * @var array
     */
    private $field;

    /**
     * @var array
     */
    private $children;

    /**
     * Node constructor.
     * @param array $field
     * @param array $children
     */
    public function __construct(
        array $field,
        array $children
    ) {
        $this->field = $field;
        $this->children = $children;
    }

    /**
     * @return Node[]
     */
    public function getChildren() : array
    {
        return $this->children;
    }

    /**
     * @return array
     */
    public function getField() : array
    {
        return $this->field;
    }
}
