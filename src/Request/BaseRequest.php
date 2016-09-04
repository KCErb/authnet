<?php

namespace mglaman\AuthNet\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use mglaman\AuthNet\AuthNetConfiguration;
use mglaman\AuthNet\Exception\AuthNetException;

/**
 * Class AuthNetRequest
 * @package mglaman\AuthNet\Request
 */
abstract class BaseRequest implements RequestInterface
{
    /**
     * @var \mglaman\AuthNet\AuthNetConfiguration
     */
    protected $configuration;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * BaseRequest constructor.
     * @param \mglaman\AuthNet\AuthNetConfiguration $configuration
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(AuthNetConfiguration $configuration, Client $client)
    {
        $this->configuration = $configuration;
        $this->client = $client;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \mglaman\AuthNet\Exception\AuthNetException
     */
    protected function sendRequest()
    {
        $postUrl = ($this->configuration->getSandbox()) ? static::getSandboxUrl() : static::getLiveUrl();
        $opts = $this->requestOptions();

        try {
            $response = $this->client->post($postUrl, $opts);

            if ($response->getStatusCode() != 200) {
                throw new AuthNetException("The request returned with error code {$response->getStatusCode()}");
            } elseif (!$response->getBody()) {
                throw new AuthNetException("The request did not have a body");
            }
        } catch (RequestException $e) {
            throw new AuthNetException($e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }

    /**
     * @return array
     */
    protected function requestOptions()
    {
        $opts = [
          'verify' => __DIR__ . '/../../resources/cert.pem',
        ];
        return $opts;
    }
}
