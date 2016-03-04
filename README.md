# Client for Apigility REST and RPC webservices

## Configuration for Zend Framework 2

### Register Abstract Service Factory

Register `Jolicht\\ApigilityClient\\ClientAbstractServiceFactory` as abstract service in service manager:

    'service_manager' => [
        'abstract_factories' => [
            'Jolicht\\ApigilityClient\\ClientAbstractServiceFactory'
        ]
    ],

### Configure Apigility Http Client

Add config key `apigility_http_client` to your prefered config file. 
(Applicaction Level e.g.: 'config/autoload/global.php; Module level e.g.:  config/module.config.php

    'apigility_http_client' => [
        'NameOfFirstClient' => [
            'base_uri' => 'http://your-apigility-service-uri.dev',
                'default_headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'http_client_options' => [
                    'timeout' => 42
                ],
            ]
        ],
        'NameOfSecondClient' => [
            'base_uri' => 'http://your-second-apigility-service-uri.dev',
                'default_headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'http_client_options' => [
                    'timeout' => 10
                ],
            ]
        ],
    ],
    
Always set `default_headers` `Accept` and `Content-Type` to prevent content type errors.

## Usage

### Get Apigility http client instance

Call `ServiceManager::get` or `ServiceLocator::get` with configured Service Name as param:

    $firstClient = $serviceManager->get('NameOfFirstClient');
    $secondClient = $serviceManager->get('NameOfSecondClient');
    
### Operations

Create:

    $response = $client->create('/api-route', $data);
    
Fetch:

    $response = $client->fetch('/api-route', 17);
    
FetchAll:

    $response =  $client->fetchAll('/api-route'); // without query data 
    $response =  $client->fetchAll('/api-route', $queryData); // with query data

Patch:

    $response = $client->patch('/api-route', 17, $data);
    
Update:

    $response = $client->update('/api-route', 17, $data);
    
Call any Http Method:

    $response = $client->call('/api-route'); // HTTP GET
    $response = $client->call('/api-route/3'); // HTTP GET
    $response = $client->call('/api-route', 'GET', $data); // HTTP GET with query parameters
    $response = $client->call('/api-route', 'POST', $data); // HTTP POST with content data
    
Not yet implemented:

* delete
* deleteList
* replaceList 