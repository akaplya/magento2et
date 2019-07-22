<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSClient\Http\Command;

use Magento\Et\Export\Processor;
use Magento\SaaSClient\Http\ClientInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class SubmitFeed
 */
class SubmitFeed
{
    /**
     * @var array
     */
    private static $configPaths = [
        'url' => 'default/magentoSaaS/url',
        'route' => 'default/magentoSaaS/route/',
        'magento-api-key' => 'default/magentoSaaS/credentials/magento-api-key'
    ];

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * SubmitFeed constructor.
     * @param Processor $processor
     * @param ClientInterface $client
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        Processor $processor,
        ClientInterface $client,
        ScopeConfigInterface $config
    ) {
        $this->processor = $processor;
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string $feedName
     * @return string
     */
    private function getUrl(string $feedName) : string
    {
        return $this->config->getValue(self::$configPaths['url'])
            . $this->config->getValue(self::$configPaths['route'] . $feedName);

    }

    /**
     * @return array
     */
    private function getHeaders() : array
    {
        return ['magento-api-key' => $this->config->getValue(self::$configPaths['magento-api-key'])];
    }

    /**
     * @param string $feedName
     * @param array $data
     * @return bool
     */
    public function execute(string $feedName) : bool
    {
        $response = $this->client->request(
            \Zend_Http_Client::PUT,
            $this->getUrl($feedName),
            $this->processor->process($feedName),
            $this->getHeaders()
        );
        $response->getStatus();
        return ($response->getStatus() == 200);
    }
}