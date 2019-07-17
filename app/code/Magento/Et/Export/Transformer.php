<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Export;

use Magento\Et\Config\ConfigInterface;

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

    /**
     * @param array $field
     * @param $value
     * @return array
     */
    private function resolveValue(array $field, $value)
    {
        if ($field['provider']) {
            return $this->transform($field, $value);
        }
        return $value;
    }

    private function isScalar(string $typeName) : bool
    {
        return in_array($typeName, ['String', 'Int', 'Float', 'ID']);
    }

    public function transform(array $rootField, array $snapshot) : array
    {
        $result = [];
        $key = base64_encode(json_encode($rootField));
        if ($this->isScalar($rootField['type'])) {
            if (isset($snapshot[$key])) {
                $field = $rootField;
                $field['provider'] = null;
                if ($rootField['repeated']) {
                    for ($i=0; count($snapshot[$key]) > $i; $i++) {
                        $result[$i] = $this->resolveValue($field, $snapshot[$key][$i][$rootField['name']]);
                    }
                } else {
                    $result = $this->resolveValue($field, $snapshot[$key][$rootField['name']]);
                }
            }
        } else {
            $type = $this->config->get($rootField['type']);
            if (isset($snapshot[$key])) {
                foreach ($type['field'] as $field) {
                    if ($rootField['repeated']) {
                        for ($i=0; count($snapshot[$key]) > $i; $i++) {
                            if (isset($snapshot[$key][$i][$field['name']])) {
                                $result[$i][$field['name']] = $this->resolveValue($field, $snapshot[$key][$i][$field['name']]);
                            } elseif ($field['provider']) {
                                $result[$i][$field['name']] = $this->transform($field, $snapshot);
                            }
                        }
                    } else {
                        if (isset($snapshot[$key][$field['name']])) {
                            $result[$field['name']] = $this->resolveValue($field, $snapshot[$key][$field['name']]);
                        } elseif ($field['provider']) {
                            $result[$field['name']] = $this->transform($field, $snapshot);
                        }
                    }
                }
            }
        }
        return $result;

    }
}
