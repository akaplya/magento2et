<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSClient\Http;

/**
 * An interface for an HTTP client.
 *
 * Sends requests via a proper adapter.
 */
interface ClientInterface
{
    /**
     * Sends a request using given parameters.
     *
     * Returns an HTTP response object or FALSE in case of failure.
     *
     * @param string $method
     * @param string $url
     * @param array $body
     * @param array $headers
     * @param string $version
     *
     * @return \Zend_Http_Response
     */
    public function request(string $method, string $url, array $body = [], array $headers = [], string $version = '1.1');
}
