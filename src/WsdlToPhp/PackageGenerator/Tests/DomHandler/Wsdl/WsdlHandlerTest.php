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

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagImport', $ebay->getImports());
        $this->assertEmpty($bing->getImports());
    }
    /**
     *
     */
    public function testGetComplexTypes()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagComplex', $ebay->getComplexTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagComplex', $bing->getComplexTypes());
    }
    /**
     *
     */
    public function testGetSimpleTypes()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagSimple', $ebay->getSimpleTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagSimple', $bing->getSimpleTypes());
    }
    /**
     *
     */
    public function testGetElements()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagElement', $bing->getElements());
    }
    /**
     *
     */
    public function testGetRestrictions()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagRestriction', $bing->getRestrictions());
    }
    /**
     *
     */
    public function testGetEnumerations()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagEnumeration', $bing->getEnumerations());
    }
    /**
     *
     */
    public function testGetInputs()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagInput', $bing->getInputs());
    }
    /**
     *
     */
    public function testGetOutputs()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagOutput', $bing->getOutputs());
    }
    /**
     *
     */
    public function testGetBodies()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagBody', $bing->getBodies());
    }
    /**
     *
     */
    public function testGetHeaders()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagHeader', $bing->getHeaders());
    }
    /**
     *
     */
    public function testGetMessages()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagMessage', $bing->getMessages());
    }
    /**
     *
     */
    public function testGetParts()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagPart', $bing->getParts());
    }
    /**
     *
     */
    public function testGetOperations()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagOperation', $bing->getOperations());
    }
    /**
     *
     */
    public function testGetDocumentations()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagDocumentation', $bing->getDocumentations());
    }
    /**
     *
     */
    public function testGetExtensions()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagExtension', $bing->getExtensions());
    }
    /**
     *
     */
    public function testGetLists()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagList', $bing->getLists());
    }
    /**
     *
     */
    public function testGetUnions()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagUnion', $bing->getunions());
    }
}
