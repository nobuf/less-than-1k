<?php

require 'Zend/Http/Client.php';
require_once 'PHPUnit/Framework.php';

class AccessPagesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Zend_Http_Client;
    }

    /**
     * testPages 
     * 
     * @dataProvider provider
     */
    public function testPages($url, $status, $needle)
    {
        $this->client->resetParameters();
        $this->client->setUri($url);
        $response = $this->client->request('GET');
        $this->assertTrue($response->getStatus() === $status);
        $this->assertTrue(strpos($response->getBody(), $needle) !== false);
    }

    public function provider()
    {
        $hostName = 'http://deadsimple.less-than-1k';
        return array(
            array($hostName . '/', 200, 'INDEX'),
            array($hostName . '/foo', 404, 'ERROR'),
            array($hostName . '/bar', 500, 'ERROR'),
            array($hostName . '/bla', 500, 'bla bla bla'),
        );
    }
}
