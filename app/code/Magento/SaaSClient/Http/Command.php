<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSClient\Http;


class Command
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(
        ClientInterface $client
    ) {
        $this->client = $client;
    }

    /**
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return int
     */
    public function execute(string $url, array $body = [], array $headers = []) : int
    {
        $response = $this->client->request(
            \Zend_Http_Client::PUT,
            $url,
            $body,
            $headers
        );
        return $response->getStatus();
    }
}