<?php
/**
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @copyright Copyright (c) 2016 Johannes Lichtenwallner (https://lichtenwallner.at)
 */
namespace Jolicht\ApigilityClient;

trait ApigilityClientAwareTrait
{
    /**
     * Apigility client
     *
     * @var Client
     */
    private $apigilityClient;

    /**
     * Get apigility client
     *
     * @return Client
     */
    public function getApigilityClient()
    {
        return $this->apigilityClient;
    }

    /**
     * Set apigility client
     *
     * @param Client $apigilityClient
     */
    public function setApigilityClient(Client $apigilityClient)
    {
        $this->apigilityClient = $apigilityClient;
    }
}