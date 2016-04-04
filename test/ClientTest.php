<?php
namespace JolichtTest\ApigilityClient;

use Jolicht\ApigilityClient\Client;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Stdlib\Parameters;
use Jolicht\ApigilityClient\ClientException;

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

    public function testGetDefaultHeaders()
    {
        $this->assertSame([], $this->client->getDefaultHeaders());
    }

    public function testGetThrowExceptions()
    {
        $this->assertFalse($this->client->getThrowExceptions());
    }

    public function testSetThrowExceptions()
    {
        $this->client->setThrowExceptions(true);
        $this->assertTrue($this->client->getThrowExceptions());
    }

    public function testGetDefaultHeadersStatedInConstructor()
    {
        $defaultHeaders = [
            'Accept' => 'application/json'
        ];
        $client = new Client($this->httpClient, 'http://test.dev', $defaultHeaders);
        $this->assertSame($defaultHeaders, $client->getDefaultHeaders());
    }

    public function testSetDefaultHeaders()
    {
        $this->client->setDefaultHeaders([
            'Accept' => 'application/json'
        ]);

        $this->assertSame(['Accept' => 'application/json'], $this->client->getDefaultHeaders());
    }

    public function testAddDefaultHeader()
    {
        $this->client->setDefaultHeaders([
            'Accept' => 'application/json'
        ]);
        $this->client->addDefaultHeader('Content-Type', 'application/json');
        $expected = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        $this->assertSame($expected, $this->client->getDefaultHeaders());
    }

    public function testCreate()
    {
        $data = [
            'testName' => 'testValue'
        ];

        $request = new Request();
        $request->setContent(json_encode($data));
        $request->setUri('http://test.dev/testapi');
        $request->setMethod(Request::METHOD_POST);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            'id' => 17,
            'testName' => 'testValue'
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $this->assertEquals((object) $responseData, $client->create('/testapi', $data));
    }

    public function testCreateInvalidStatusCodeThrowsException()
    {
        $request = new Request();
        $request->setUri('http://test.dev/testapi');
        $request->setMethod(Request::METHOD_POST);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));
        $response = new Response();
        $response->setContent(json_encode([
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Forbidden',
            'status' => 403,
            'detail' => 'Forbidden'
        ]));
        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);
        $client->setThrowExceptions(true);
        $this->expectException('Jolicht\ApigilityClient\ClientException');
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('Forbidden');
        $client->create('/testapi', []);
    }

    public function testFetch()
    {
        $request = new Request();
        $request->setUri('http://test.dev/testapi/17');
        $request->setMethod(Request::METHOD_GET);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            'id' => 17,
            'testName' => 'testValue'
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $this->assertEquals((object) $responseData, $client->fetch('/testapi', 17));
    }

    public function testFetchInvalidStatusCodeThrowsException()
    {
        $request = new Request();
        $request->setUri('http://test.dev/testapi/17');
        $request->setMethod(Request::METHOD_GET);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $response->setContent(json_encode([
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Forbidden',
            'status' => 403,
            'detail' => 'Forbidden'
        ]));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));
        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);
        $client->setThrowExceptions(true);

        $this->expectException('Jolicht\ApigilityClient\ClientException');
        $this->expectExceptionCode(403);
        $this->expectExceptionMessage('Forbidden');

        $client->fetch('/testapi', 17);
    }

    public function testFetchAll()
    {
        $queryData = [
            'query' => 'value'
        ];

        $request = new Request();
        $request->setQuery(new Parameters($queryData));
        $request->setUri('http://test.dev/testapi');
        $request->setMethod(Request::METHOD_GET);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            [
                'id' => 17,
                'testName' => 'testValue1'
            ],
            [
                'id' => 14,
                'testName' => 'testValue2'
            ],
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $expected = [
            (object) [
                'id' => 17,
                'testName' => 'testValue1'
            ],
            (object) [
                'id' => 14,
                'testName' => 'testValue2'
            ],
        ];

        $this->assertEquals($expected, $client->fetchAll('/testapi', $queryData));
    }

    public function testPatch()
    {
        $data = [
            'testName' => 'testValue'
        ];

        $request = new Request();
        $request->setContent(json_encode($data));
        $request->setUri('http://test.dev/testapi/17');
        $request->setMethod(Request::METHOD_PATCH);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            'id' => 17,
            'testName' => 'testValue'
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $this->assertEquals((object) $responseData, $client->patch('/testapi', 17, $data));
    }

    public function testUpdate()
    {
        $data = [
            'testName' => 'testValue'
        ];

        $request = new Request();
        $request->setContent(json_encode($data));
        $request->setUri('http://test.dev/testapi/17');
        $request->setMethod(Request::METHOD_PUT);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            'id' => 17,
            'testName' => 'testValue'
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $this->assertEquals((object) $responseData, $client->update('/testapi', 17, $data));
    }

    public function testCallHttpGet()
    {
        $request = new Request();
        $request->setUri('http://test.dev/testapi/23');
        $request->setMethod(Request::METHOD_GET);
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            'id' => 17,
            'testName' => 'testValue'
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $this->assertEquals((object) $responseData, $client->call('/testapi/23'));;
    }

    public function testCallHttpGetWithQueryParameters()
    {
        $queryData = [
            'query' => 'value'
        ];

        $request = new Request();
        $request->setUri('http://test.dev/testapi');
        $request->setMethod(Request::METHOD_GET);
        $request->setQuery(new Parameters($queryData));
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            'id' => 17,
            'testName' => 'testValue'
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $this->assertEquals((object) $responseData, $client->call('/testapi', Request::METHOD_GET, $queryData));
    }

    public function testCallHttpPost()
    {
        $data = [
            'testName' => 'testValue'
        ];

        $request = new Request();
        $request->setUri('http://test.dev/testapi');
        $request->setMethod(Request::METHOD_POST);
        $request->setContent(json_encode($data));
        $request->getHeaders()->addHeaders(array(
            'Accept' => 'application/json',
        ));

        $response = new Response();
        $responseData = [
            'id' => 17,
            'testName' => 'testValue'
        ];
        $response->setContent(json_encode($responseData));

        $httpClient = $this->getMock('Zend\Http\Client', array('send'));
        $httpClient->expects($this->once())
                         ->method('send')
                         ->with($this->equalTo($request))
                         ->will($this->returnValue($response));

        $client = new Client($httpClient, 'http://test.dev/', ['Accept' => 'application/json']);

        $this->assertEquals((object) $responseData, $client->call('/testapi', Request::METHOD_POST, $data));
    }
}