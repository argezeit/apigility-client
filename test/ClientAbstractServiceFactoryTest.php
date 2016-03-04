<?php
namespace JolichtTest\ApigilityClient;

use Jolicht\ApigilityClient\ClientAbstractServiceFactory;

class ClientAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $factory, $serviceLocator;

    protected function setUp()
    {
        $this->factory = new ClientAbstractServiceFactory();
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface', array('get', 'has'));
    }

    public function testSetConfig()
    {
        $config = ['testKey' => 'testValue'];
        $this->factory->setConfig($config);
        $this->assertSame($config, $this->factory->getConfig($this->serviceLocator));
    }

    public function testGetConfigServiceLocatorHasNoConfigReturnsEmptyArray()
    {
        $this->serviceLocator->expects($this->once())
                             ->method('has')
                             ->with($this->equalTo('config'))
                             ->will($this->returnValue(false));
        $this->assertSame([], $this->factory->getConfig($this->serviceLocator));
    }

    public function testGetConfigServiceLocatorConfigHasNoConfigKeyReturnsEmptyArray()
    {
        $this->serviceLocator->expects($this->once())
                             ->method('has')
                             ->with($this->equalTo('config'))
                             ->will($this->returnValue(true));
        $this->serviceLocator->expects($this->once())
                             ->method('get')
                             ->with($this->equalTo('config'))
                             ->will($this->returnValue(['testKey' => 'testValue']));
        $this->assertSame([], $this->factory->getConfig($this->serviceLocator));
    }

    public function testGetConfigServiceLocatorConfigHasConfigKey()
    {
        $config = ['apigility_http_client' => ['testKey' => 'testValue']];

        $this->serviceLocator->expects($this->once())
                             ->method('has')
                             ->with($this->equalTo('config'))
                             ->will($this->returnValue(true));
        $this->serviceLocator->expects($this->once())
                             ->method('get')
                             ->with($this->equalTo('config'))
                             ->will($this->returnValue($config));
        $this->assertSame(['testKey' => 'testValue'] , $this->factory->getConfig($this->serviceLocator));
    }

    public function testCanCreateServiceWithNameNoConfigReturnsFalse()
    {
        $this->factory->setConfig([]);
        $this->assertFalse($this->factory->canCreateServiceWithName($this->serviceLocator, 'testName', 'testRequestedName'));
    }

    public function testCanCreateServiceWithNameConfigHasNotRequestedNameReturnsFalse()
    {
        $config = ['apigility_http_client' => ['testKey' => 'testValue']];
        $this->factory->setConfig($config);
        $this->assertFalse($this->factory->canCreateServiceWithName($this->serviceLocator, 'testName', 'testRequestedName'));
    }

    public function testCanCreateServiceWithNameConfigHasRequestedName()
    {
        $config = ['testRequestedName' => 'testValue'];
        $this->factory->setConfig($config);
        $this->assertTrue($this->factory->canCreateServiceWithName($this->serviceLocator, 'testName', 'testRequestedName'));
    }

    public function testCreateServiceWithName()
    {
        $config = [
            'clientName' => [
                'base_uri' => 'http://test.dev',
                'default_headers' => [
                    'Accept' => 'application/json',
                ],
                'http_client_options' => [
                    'timeout' => 42
                ],

            ]
        ];
        $this->factory->setConfig($config);
        $client = $this->factory->createServiceWithName($this->serviceLocator, 'testName', 'clientName');
        $this->assertSame(['Accept' => 'application/json'], $client->getDefaultHeaders());
        $httpClient = $client->getHttpClient();
        $httpClientConfig = $httpClient->getAdapter()->getConfig();
        $this->assertSame(42, $httpClientConfig['timeout']);
    }
}