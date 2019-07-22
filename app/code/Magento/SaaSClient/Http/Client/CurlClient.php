<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSClient\Http\Client;

use Magento\SaaSClient\Http\ConverterInterface;
use Magento\SaaSClient\Http\ClientInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\HTTP\ResponseFactory;
use Psr\Log\LoggerInterface;

/**
 * A CURL HTTP client.
 *
 * Sends requests via a CURL adapter.
 */
class CurlClient implements ClientInterface
{
    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ConverterInterface
     */
    private $converter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CurlFactory $curlFactory
     * @param ResponseFactory $responseFactory
     * @param ConverterInterface $converter
     * @param LoggerInterface $logger
     */
    public function __construct(
        CurlFactory $curlFactory,
        ResponseFactory $responseFactory,
        ConverterInterface $converter,
        LoggerInterface $logger
    ) {
        $this->curlFactory = $curlFactory;
        $this->responseFactory = $responseFactory;
        $this->converter = $converter;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $url, array $body = [], array $headers = [], string $version = '1.1')
    {
        $response = new \Zend_Http_Response(0, []);

        try {
            $curl = $this->curlFactory->create();
            $headers = $this->applyContentTypeHeaderFromConverter($headers);

            $curl->write($method, $url, $version, $headers, $this->converter->toBody($body));

            $result = $curl->read();

            if ($curl->getErrno()) {
                $this->logger->critical(
                    new \Exception(
                        sprintf(
                            'SasS service CURL connection error #%s: %s',
                            $curl->getErrno(),
                            $curl->getError()
                        )
                    )
                );

                return $response;
            }

            $response = $this->responseFactory->create($result);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $response;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function applyContentTypeHeaderFromConverter(array $headers)
    {
        $contentTypeHeaderKey = array_search($this->converter->getContentTypeHeader(), $headers);
        if ($contentTypeHeaderKey === false) {
            $headers[] = $this->converter->getContentTypeHeader();
        }

        return $headers;
    }
}
