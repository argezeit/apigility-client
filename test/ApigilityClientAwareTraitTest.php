<?php

namespace JolichtTest\ApigilityClient;

class ApigilityClientAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    private $trait;

    protected function setUp()
    {
        $this->trait = $this->getMockForTrait('Jolicht\ApigilityClient\ApigilityClientAwareTrait');
    }

    public function testSetApigilityClient()
    {
        $client = $this->getMockWithoutInvokingTheOriginalConstructor('Jolicht\ApigilityClient\Client');
        $this->trait->setApigilityClient($client);
        $this->assertSame($client, $this->trait->getApigilityClient());
    }
}