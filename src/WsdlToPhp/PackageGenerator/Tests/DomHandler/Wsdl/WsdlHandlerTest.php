<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl;
use WsdlToPhp\PackageGenerator\Tests\TestCase;

class WsdlHandlerTest extends TestCase
{
    protected static $ebayInstance;
    protected static $bingInstance;
    /**
     * @return WsdlHandler
     */
    public static function eBayInstance()
    {
        if (!isset(self::$ebayInstance)) {
            $doc = new \DOMDocument('1.0', 'utf-8');
            $doc->load(dirname(__FILE__) . '/../../resources/ebaySvc.wsdl');
            self::$ebayInstance = new Wsdl($doc);
        }
        return self::$ebayInstance;
    }
    /**
     * @return WsdlHandler
     */
    public static function bingInstance()
    {
        if (!isset(self::$bingInstance)) {
            $doc = new \DOMDocument('1.0', 'utf-8');
            $doc->load(dirname(__FILE__) . '/../../resources/bingsearch.wsdl');
            self::$bingInstance = new Wsdl($doc);
        }
        return self::$bingInstance;
    }
    /**
     *
     */
    public function testGetImports()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Import', $ebay->getImports());
        $this->assertEmpty($bing->getImports());
    }
    /**
     *
     */
    public function testGetComplexTypes()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Complex', $ebay->getComplexTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Complex', $bing->getComplexTypes());
    }
    /**
     *
     */
    public function testGetSimpleTypes()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Simple', $ebay->getSimpleTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Simple', $bing->getSimpleTypes());
    }
    /**
     *
     */
    public function testGetElements()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Element', $bing->getElements());
    }
    /**
     *
     */
    public function testGetRestrictions()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Restriction', $bing->getRestrictions());
    }
    /**
     *
     */
    public function testGetEnumerations()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Enumeration', $bing->getEnumerations());
    }
    /**
     *
     */
    public function testGetInputs()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Input', $bing->getInputs());
    }
    /**
     *
     */
    public function testGetOutputs()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Output', $bing->getOutputs());
    }
    /**
     *
     */
    public function testGetBodies()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Body', $bing->getBodies());
    }
    /**
     *
     */
    public function testGetHeaders()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Header', $bing->getHeaders());
    }
    /**
     *
     */
    public function testGetMessages()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Message', $bing->getMessages());
    }
    /**
     *
     */
    public function testGetParts()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Part', $bing->getParts());
    }
    /**
     *
     */
    public function testGetOperations()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Operation', $bing->getOperations());
    }
}
