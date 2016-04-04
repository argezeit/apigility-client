<?php
/**
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @copyright Copyright (c) 2016 Johannes Lichtenwallner (https://lichtenwallner.at)
 */
namespace Jolicht\ApigilityClient;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as HttpClient;

class ClientAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * Service factory config
     *
     * @var array
     */
    private $config;

    /**
     * Config key
     *
     * @var string
     */
    private $configKey = 'apigility_http_client';

    /**
     * Set config
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get config
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     */
    public function getConfig(ServiceLocatorInterface $serviceLocator)
    {
        if (null !== $this->config) {
            return $this->config;
        }

        if (false === $serviceLocator->has('config')) {
            $this->config = [];
            return $this->config;
        }

        $config = $serviceLocator->get('config');
        if (!isset($config[$this->configKey])) {
            $this->config = [];
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator);
        if (empty($config)) {
            return false;
        }
        return isset($config[$requestedName]);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $allConfigs = $this->getConfig($serviceLocator);
        $config = $allConfigs[$requestedName];
        $httpClient = new HttpClient();
        if (isset($config['http_client_options'])) {
            $httpClient->setOptions($config['http_client_options']);
        }
        $client = new Client($httpClient, $config['base_uri']);
        if (isset($config['default_headers'])) {
            $client->setDefaultHeaders($config['default_headers']);
        }
        if (isset($config['throw_exceptions'])) {
            $client->setThrowExceptions($config['throw_exceptions']);
        }
        return $client;
    }
}