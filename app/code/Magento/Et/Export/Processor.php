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
     * @param array $arguments
     * @return array
     */
    public function process(string $fieldName, array $arguments = []) : array
    {
        $info = $this->infoAssembler->assembleFieldInfo($fieldName, $this->rootProfileName);
//        $key = base64_encode(json_encode($info->getRootNode()->getField()));
        $snapshots = $this->extractor->extract($info, $arguments);
//        $snapshots[$key]
        $data = $this->transformer->transform($info->getRootNode()->getField(), $snapshots);
        return
            $data;
    }
}
