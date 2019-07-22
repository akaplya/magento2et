<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSClient\Http;

/**
 * Represents converter interface for http request and response body.
 *
 * @api
 */
interface ConverterInterface
{
    /**
     * @param string $body
     *
     * @return array
     */
    public function fromBody($body): array;

    /**
     * @param array $data
     *
     * @return string
     */
    public function toBody(array $data): string;

    /**
     * @return string
     */
    public function getContentTypeHeader(): string;

    /**
     * @return string
     */
    public function getContentMediaType(): string;
}
