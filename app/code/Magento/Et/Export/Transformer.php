<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Export;

use Magento\Et\Config\ConfigInterface;
use Magento\Et\Export\Request\Info;

/**
 * Class Transformer
 * @package Magento\Et\Export
 */
class Transformer
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Transformer constructor.
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    private function isScalar(string $typeName) : bool
    {
        return in_array($typeName, ['String', 'Int', 'Float', 'ID']);
    }

    /**
     * @param array $rootField
     * @param array $snapshot
     * @return array
     */
    public function transform(Info $info, array $snapshot) : array
    {
        $result = [];
        $key = $this->getKey($info->getRootNode()->getField());
        if (!isset($snapshot[$key])) {
            return $result;
        }
        $data = $this->convertComplexData(
            $info->getRootNode()->getField(), $snapshot, null);
        return $data;
    }

    /**
     * @param array $field
     * @return string
     */
    private function getKey(array $field) : string
    {
        return base64_encode(json_encode($field));
    }

    private function castToFieldType(array $field, $value) {
        return $value;
    }

    /**
     * @param array $row
     * @param array $type
     * @param array $snapshot
     * @return array
     */
    private function convertComplexRow(array $row, array $type, array $snapshot) : array
    {
        $result = [];
        foreach ($type['field'] as $field) {
            if ($field['provider'] != null) {
                $key = $this->getKey($field);
                if (isset($snapshot[$key])) {
                    $index = [];
                    foreach ($field['using'] as $key) {
                        $index[] = [$key['field'] => $row[$key['field']]];
                    }
                    $lookupReference = base64_encode(json_encode($index));
                    $result[$field['name']] = $this->convertComplexData($field, $snapshot, $lookupReference); //todo: add Filter cond
                }
            } else {
                if (isset($row[$field['name']])) {
                    $result[$field['name']] = $this->castToFieldType($field, $row[$field['name']]);
                }
            }
        }
        return $result;
    }

    /**
     * TODO: rename to covert data that has type and provider
     *
     * @param array $field
     * @param array $snapshot
     * @param string $lookup
     * @return array
     */
    private function convertComplexData(array $field, array $snapshot, ?string $lookup) : ?array
    {
        if ($lookup) {
            if (!isset($snapshot[$this->getKey($field)][$lookup])) {
                return null;
            }
            $data = $snapshot[$this->getKey($field)][$lookup];    
        } else {
            $data = $snapshot[$this->getKey($field)];
        }
        $result = null;
        if ($this->isScalar($field['type'])) {
            if ($field['repeated']) {
                for ($i=0; $i < count($data); $i++) {
                    $result[$i] = $this->castToFieldType($field, $data[$i]) ;
                }
            } else {
                $result = $this->castToFieldType($field, $data[$i]);
            }
        } else {
            $type = $this->config->get($field['type']);
            if ($field['repeated']) {
                for ($i=0; $i < count($data); $i++) {
                    $result[$i] = $this->convertComplexRow($data[$i], $type, $snapshot) ;
                }
            } else {
                $result = $this->convertComplexRow($data, $type, $snapshot);
            }
        }
        return $result;
    }
}
