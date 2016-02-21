<?php
/**
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @copyright Copyright (c) 2016 Johannes Lichtenwallner (https://lichtenwallner.at)
 */

namespace Jolicht\ApigilityClient;

class Module
{
    /**
     * get autoloader config
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/',
                ],
            ],
        ];
    }

    /**
     * Get module config
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}