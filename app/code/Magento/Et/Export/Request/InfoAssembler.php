<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Export\Request;

use Magento\Et\Config\ConfigInterface;

/**
 * Class InfoAssembler
 */
class InfoAssembler
{

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var InfoFactory
     */
    private $infoFactory;

    /**
     * InfoAssembler constructor.
     * @param ConfigInterface $config
     * @param NodeFactory $nodeFactory
     * @param InfoFactory $infoFactory
     */
    public function __construct(
        ConfigInterface $config,
        NodeFactory $nodeFactory,
        InfoFactory $infoFactory

    ) {
        $this->config = $config;
        $this->nodeFactory = $nodeFactory;
        $this->infoFactory = $infoFactory;
    }

    /**
     * @param array $field
     * @return Node
     */
    private function assembleNode(array $field) : Node
    {
        $children = [];
        if ($this->isScalar($field['type'])) {
            return $this->nodeFactory->create(
                [
                    'field' => $field,
                    'children' => []
                ]
            );
        } else {
            $type = $this->config->get($field['type']);
            foreach ($type['field'] as $item) {
                if (isset($item['provider'])) {
                    $children[$item['name']] = $this->assembleNode($item);
                }
            }
            return $this->nodeFactory->create(
                [
                    'field' => $field,
                    'children' => $children
                ]
            );
        }

    }

    /**
     * @param string $fieldName
     * @param string $parentTypeName
     * @return Info
     */
    public function assembleFieldInfo(string $fieldName, string $parentTypeName) : Info
    {
        $export = $this->config->get($parentTypeName);
        $node = $this->assembleNode($export['field'][$fieldName]);
        return $this->infoFactory->create(['node' => $node]);
    }

    /**
     * @param string $typeName
     * @return bool
     */
    private function isScalar(string $typeName) : bool
    {
        return in_array($typeName, ['String', 'Int', 'Float', 'ID']);
    }
}
