<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Export;

use Magento\Et\Api\UglyExportWrapperInterface;

/**
 * Class UglyExportWrapper
 */
class UglyExportWrapper implements UglyExportWrapperInterface
{

    /**
     * @var Processor
     */
    private $processor;

    /**
     * UglyExportWrapper constructor.
     * @param Processor $processor
     */
    public function __construct(
        Processor $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * @param string $profileName
     * @return string
     */
    public function export(string $profileName) : string
    {
        return json_encode($this->processor->process($profileName), JSON_PRETTY_PRINT);
    }
}