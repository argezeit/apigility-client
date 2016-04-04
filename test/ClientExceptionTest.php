<?php

namespace JolichtTest\ApigilityClient;

use Jolicht\ApigilityClient\ClientException;

class ClientExceptionTest extends \PHPUnit_Framework_TestCase
{
    private $e;

    protected function setUp()
    {
        $this->e = new ClientException('testMessage', 42);
    }

    public function testGetMessage()
    {
        $this->assertSame('testMessage', $this->e->getMessage());
    }

    public function testGetCode()
    {
        $this->assertSame(42, $this->e->getCode());
    }
}