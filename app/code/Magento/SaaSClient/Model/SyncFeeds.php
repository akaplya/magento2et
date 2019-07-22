<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSClient\Model;

use Magento\SaaSClient\Http\Command\SubmitFeed;
use Magento\SaaSClient\Api\SyncFeedsInterface;

/**
 * Class SyncFeeds
 */
class SyncFeeds implements SyncFeedsInterface
{
    /**
     * @var SubmitFeed
     */
    private $submitFeed;

    public function __construct(
        SubmitFeed $submitFeed
    ) {
        $this->submitFeed = $submitFeed;
    }

    /**
     * @return bool
     */
    public function execute() : bool
    {
        $this->submitFeed->execute('products');
        return true;
    }
}