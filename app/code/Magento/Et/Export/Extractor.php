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
     * Processor constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $field
     * @param array $value
     * @return array
     */
    private function resolveArguments(array $field, array $value) : array
    {
        $arguments = [];
        foreach ($value as $item) {
            $argument = [];
            foreach ($field['using'] as $using) {
                $argument[$using['field']] = $item[$using['field']];
            }
            $arguments[] = $argument;
        }
        return $arguments;
    }

    /**
     * @param Node $node
     * @param array $value
     * @return array
     */
    private function extractDataForNode(Node $node, array $value) : array
    {
        $output = [];
        $key = base64_encode(json_encode($node->getField()));
        $providerClass = $node->getField()['provider'];
        $provider = $this->objectManager->get($providerClass);
        $data = $provider->get($value);
        foreach ($node->getChildren() as $child) {
            $args = $this->resolveArguments($child->getField(), $data);
            $output = array_replace_recursive($output, $this->extractDataForNode($child, $args));
        }
        $output[$key] = $data;
        return $output;
    }

    /**
     * @param Info $info
     * @return array
     */
    public function extract(Info $info) : array
    {
        $data = $this->extractDataForNode($info->getRootNode(), []);
        return $data;
    }
}
