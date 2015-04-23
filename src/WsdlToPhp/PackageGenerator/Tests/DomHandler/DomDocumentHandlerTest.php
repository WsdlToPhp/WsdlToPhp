<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\DomHandler\DomDocumentHandler;

class DomDocumentHandlerTest extends TestCase
{
    protected static $instance;
    /**
     * @return DomDocumentHandler
     */
    public static function eBayInstance()
    {
        if (!isset(self::$instance)) {
            $doc = new \DOMDocument('1.0', 'utf-8');
            $doc->load(dirname(__FILE__) . '/../resources/ebaySvc.wsdl');
            self::$instance = new DomDocumentHandler($doc);
        }
        return self::$instance;
    }
    /**
     * @return DomDocumentHandler
     */
    public static function bingInstance()
    {
        if (!isset(self::$instance)) {
            $doc = new \DOMDocument('1.0', 'utf-8');
            $doc->load(dirname(__FILE__) . '/../resources/bingsearch.wsdl');
            self::$instance = new DomDocumentHandler($doc);
        }
        return self::$instance;
    }
    /**
     *
     */
    public function testGetNodeByName()
    {
        $instance = self::bingInstance();

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\NodeHandler', $instance->getNodeByName('types'));
        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\NodeHandler', $instance->getNodeByName('definitions'));
        $this->assertNull($instance->getNodeByName('foo'));
    }
    /**
     *
     */
    public function testGetNodesByName()
    {
        $instance = self::bingInstance();

        $this->assertNotEmpty($instance->getNodesByName('element'));
        $this->assertEmpty($instance->getNodesByName('foo'));
    }
}
