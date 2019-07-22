<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Api;

/**
 * Interface Processor
 * Temporary, To test service through the REST
 */
interface UglyExportWrapperInterface
{
    /**
     * @param string $profileName
     * @return string
     */
    public function export(string $profileName) : string;
}