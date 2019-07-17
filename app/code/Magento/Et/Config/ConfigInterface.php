<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Config;

/**
 * Interface ConfigInterface
 */
interface ConfigInterface
{

    /**
     * @param string $profileName
     * @return array
     */
    public function get(string $profileName) : array;
}