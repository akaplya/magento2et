<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Export;

use Magento\Et\Export\Request\InfoAssembler;

/**
 * Class Processor
 * @package Magento\Et\Export
 */
class Processor
{
    /**
     * @var Extractor
     */
    private $extractor;

    /**
     * @var Transformer
     */
    private $transformer;

    /**
     * @var InfoAssembler
     */
    private $infoAssembler;

    /**
     * @var string
     */
    private $rootProfileName;

    /**
     * Processor constructor.
     *
     * @param Extractor $extractor
     * @param Transformer $transformer
     * @param InfoAssembler $infoAssembler
     * @param string $rootProfileName
     */
    public function __construct(
        Extractor $extractor,
        Transformer $transformer,
        InfoAssembler $infoAssembler,
        string $rootProfileName = 'Export'
    ) {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->infoAssembler = $infoAssembler;
        $this->rootProfileName = $rootProfileName;
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function process(string $fieldName) : array
    {
        $info = $this->infoAssembler->assembleFieldInfo($fieldName, $this->rootProfileName);
        $snapshots = $this->extractor->extract($info);
        $data = $this->transformer->transform($info->getRootNode()->getField(), $snapshots);
        return $data;
    }


}