<?php
namespace JolichtTest\ApigilityClient\Client;

use Jolicht\ApigilityClient\Client;
use Zend\Http\Client as HttpClient;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    private $client, $httpClient;

    protected function setUp()
    {
        $this->httpClient = new HttpClient();
        $this->client = new Client($this->httpClient, 'http://test.dev/');
    }

    public function testGetHttpClient()
    {
        $this->assertSame($this->httpClient, $this->client->getHttpClient());
    }

    public function testGetBaseUri()
    {
        $this->assertSame('http://test.dev', $this->client->getBaseUri());
    }
}