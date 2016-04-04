<?php
/**
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @copyright Copyright (c) 2016 Johannes Lichtenwallner (https://lichtenwallner.at)
 */
namespace Jolicht\ApigilityClient;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

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
     * Default headers
     *
     * @var array
     */
    private $defaultHeaders = [];

    /**
     * Throw exceptions
     *
     * @var boolean
     */
    private $throwExceptions = false;

    /**
     * Constructor
     *
     * @param HttpClient $httpClient
     * @param string $baseUri
     * @param array $defaultHeaders
     */
    public function __construct(HttpClient $httpClient, $baseUri, array $defaultHeaders = [])
    {
        $this->httpClient = $httpClient;
        $this->baseUri = rtrim($baseUri, '/');
        $this->defaultHeaders = $defaultHeaders;
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

    /**
     * Get default headers
     *
     * @return array
     */
    public function getDefaultHeaders()
    {
        return $this->defaultHeaders;
    }

    /**
     * Set default headers
     *
     * @param array $defaultHeaders
     */
    public function setDefaultHeaders(array $defaultHeaders)
    {
        $this->defaultHeaders = $defaultHeaders;
    }

    /**
     * Add default header
     *
     * @param string $name
     * @param string $value
     */
    public function addDefaultHeader($name, $value)
    {
        $this->defaultHeaders[$name] = $value;
    }

    /**
     * Get throw exceptions
     *
     * @return boolean
     */
    public function getThrowExceptions()
    {
        return $this->throwExceptions;
    }

    /**
     * Set throw exceptions
     *
     * @param boolean $throwExceptions
     */
    public function setThrowExceptions($throwExceptions)
    {
        $this->throwExceptions = $throwExceptions;
    }

    /**
     * Create
     *
     * @param string $api
     * @param array $data
     */
    public function create($api, array $data)
    {
        return $this->checkException($this->callHttpMethod($this->normalizeUri($api), Request::METHOD_POST, $data));
    }

    public function delete()
    {
        throw new \Exception('not yet implemented');
    }

    public function deleteList()
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * Fetch
     *
     * @param string $api
     * @param int|string $id
     */
    public function fetch($api, $id)
    {
        return $this->checkException($this->callHttpMethod($this->normalizeUri($api, $id), Request::METHOD_GET));
    }

    /**
     * Fetch all
     *
     * @param string $api
     * @param array $queryData
     */
    public function fetchAll($api, array $queryData = [])
    {
        return $this->checkException($this->callHttpMethod($this->normalizeUri($api), Request::METHOD_GET, $queryData));
    }

    /**
     * Patch
     *
     * @param string $api
     * @param int|string $id
     * @param array $data
     */
    public function patch($api, $id, array $data = [])
    {
        return $this->checkException($this->callHttpMethod($this->normalizeUri($api, $id), Request::METHOD_PATCH, $data));
    }

    /**
     * Update
     *
     * @param string $api
     * @param int|string $id
     * @param array $data
     */
    public function update($api, $id, array $data = [])
    {
        return $this->checkException($this->callHttpMethod($this->normalizeUri($api, $id), Request::METHOD_PUT, $data));
    }

    /**
     * Call http method
     *
     * @param string $api
     * @param string $method
     * @param array $data
     */
    public function call($api, $method = Request::METHOD_GET, array $data = [])
    {
        return $this->checkException($this->callHttpMethod($this->normalizeUri($api), $method, $data));
    }

    /**
     * Check if exception should be thrown
     * @param object $response
     * @return object
     * @throws ClientException
     */
    private function checkException($response)
    {
        if ((true === $this->getThrowExceptions()) and ($this->isErroneousResponse($response))) {
            throw new ClientException($response->detail, $response->status);
        }
        return $response;
    }

    /**
     * is erroneous reponse
     *
     * @param object $response
     * @return boolean
     */
    private function isErroneousResponse($response)
    {
        return (isset($response->type) and isset($response->title) and isset($response->status) and isset($response->detail));
    }

    /**
     * Call http method
     *
     * @param string $uri
     * @param string $method
     * @param array $data
     */
    private function callHttpMethod($uri, $method, array $data = [])
    {
        $request = new Request();
        $request->setUri($uri);
        if (! empty($data)) {
            switch ($method) {
                case Request::METHOD_GET:
                    $request->setQuery(new Parameters($data));
                    break;
                case Request::METHOD_POST:
                case Request::METHOD_PUT:
                case Request::METHOD_PATCH:
                    $request->setContent(json_encode($data));
                    break;
            }
        }
        $request->setMethod($method);
        $request->getHeaders()->addHeaders($this->getDefaultHeaders());
        return json_decode($this->getHttpClient()->send($request)->getContent());
    }

    /**
     * Normalize uri
     *
     * @param string $api
     * @return string
     */
    private function normalizeUri($api, $id = null)
    {
        if (null === $id) {
            return $this->getBaseUri() . '/' . ltrim($api, '/');
        } else {
            return $this->getBaseUri() . '/' . trim($api, '/') . '/' . $id;
        }
    }
}