<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Et\Config;

use Magento\Framework\Config\DataInterface;

/**
 * Config of ReportXml
 */
class Config implements ConfigInterface
{
    /**
     * @var DataInterface
     */
    private $data;

    /**
     * Config constructor.
     *
     * @param DataInterface $data
     */
    public function __construct(
        DataInterface $data
    ) {
        $this->data = $data;
    }

    /**
     * @param string $profileName
     * @return array
     */
    public function get(string $profileName) : array
    {
        return $this->data->get($profileName);
    }
}
