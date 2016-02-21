<?php
/**
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @copyright Copyright (c) 2016 Johannes Lichtenwallner (https://lichtenwallner.at)
 */
namespace Jolicht\ApigilityClient;

use Zend\Http\Client as HttpClient;

class Client
{

    /**
     * Http client
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Base Uri
     *
     * @var string
     */
    private $baseUri;

    /**
     * Constructor
     *
     * @param HttpClient $httpClient
     * @param string $baseUri
     */
    public function __construct(HttpClient $httpClient, $baseUri)
    {
        $this->httpClient = $httpClient;
        $this->baseUri    = rtrim($baseUri, '/');
    }

    /**
     * Get http client
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Get base uri
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }
}