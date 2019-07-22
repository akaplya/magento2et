<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSClient\Api;


/**
 * Interface SyncFeedsInterface
 */
interface SyncFeedsInterface
{

    /**
     * @return bool
     */
    public function execute() : bool;
}
