<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Export;


use Magento\Et\Export\Request\Info;
use Magento\Et\Export\Request\Node;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Extractor
 * @package Magento\Et\Export
 */
class Extractor
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Extractor constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $parentField
     * @param array $field
     * @param array $value
     * @return array
     */
    private function resolveArguments(array $parentField, array $field, array $value, bool $isRoot) : array
    {
        $arguments = [];
        if ($isRoot) {
            return  $value;
        }
        foreach ($value as $item) {
            $argument = [];
            foreach ($field['using'] as $using) {
                if ($parentField['repeated'] && !$this->isIdLookup($parentField)) {
                    foreach ($item as $row) {
                        $argument[$using['field']] = $row[$using['field']];
                    }
                } else {
                    $argument[$using['field']] = $item[$using['field']];
                }

            }
            $arguments[] = $argument;
        }
        return $arguments;
    }

    /**
     * @param string $typeName
     * @return bool
     */
    private function isScalar(string $typeName) : bool
    {
        return in_array($typeName, ['String', 'Int', 'Float', 'ID']);
    }

    /**
     * @param array $field
     * @param array $data
     * @param bool $isRoot
     * @return array
     */
    private function indexDataByArguments(array $field, array $data, bool $isRoot) : array
    {
        $output = [];
        if($isRoot) {
            return $data;
        }
        if ($field['repeated'] && !$isRoot) {
            foreach ($data as $item) {
                $index = [];
                foreach ($field['using'] as $key) {
                    $index[] = [$key['field'] => $item[$key['field']]];
                }
                $output[base64_encode(json_encode($index))][] = $item;
            }
        } else {
            foreach ($data as $item) {
                $index = [];
                foreach ($field['using'] as $key) {
                    $index[] = [$key['field'] => $item[$key['field']]];
                }
                $output[base64_encode(json_encode($index))] = $item;
            }
        }
        return $output;
    }

    /**
     * @param Info $info
     * @param Node $node
     * @param array $value
     * @return array
     */
    private function extractDataForNode(Info $info, Node $node, array $value) : array
    {
        $output = [];
        $isRoot = (spl_object_hash($info->getRootNode()) == spl_object_hash($node));
        $key = base64_encode(json_encode($node->getField()));
        $providerClass = $node->getField()['provider'];
        $provider = $this->objectManager->get($providerClass);
        $data = $this->indexDataByArguments($node->getField(),$provider->get($value), $isRoot);
        foreach ($node->getChildren() as $child) {
            $args = $this->resolveArguments($node->getField(), $child->getField(), $data, $isRoot);
            $output = array_replace_recursive(
                $output,
                $this->extractDataForNode($info, $child, $args)
            );
        }
        $output[$key] = $data;
        return $output;
    }

    /**
     * @param Info $info
     * @param array $arguments
     * @return array
     */
    public function extract(Info $info, array $arguments = []) : array
    {
        $data = $this->extractDataForNode($info, $info->getRootNode(), $arguments);
        return $data;
    }
}
