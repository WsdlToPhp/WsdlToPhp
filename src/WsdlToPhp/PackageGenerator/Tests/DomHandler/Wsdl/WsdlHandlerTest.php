<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl;
use WsdlToPhp\PackageGenerator\Tests\TestCase;

class WsdlHandlerTest extends TestCase
{
    protected static $ebayInstance;
    protected static $bingInstance;
    protected static $partnerInstance;
    protected static $aukroInstance;
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
     * @return WsdlHandler
     */
    public static function partnerInstance()
    {
        if (!isset(self::$partnerInstance)) {
            $doc = new \DOMDocument('1.0', 'utf-8');
            $doc->load(dirname(__FILE__) . '/../../resources/PartnerService.wsdl');
            self::$partnerInstance = new Wsdl($doc);
        }
        return self::$partnerInstance;
    }
    /**
     * @return WsdlHandler
     */
    public static function aukroInstance()
    {
        if (!isset(self::$aukroInstance)) {
            $doc = new \DOMDocument('1.0', 'utf-8');
            $doc->load(dirname(__FILE__) . '/../../resources/aukro.wsdl');
            self::$aukroInstance = new Wsdl($doc);
        }
        return self::$aukroInstance;
    }
    /**
     *
     */
    public function testGetImports()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();
        $partner = self::partnerInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagImport', $partner->getImports());
        $this->assertEmpty($bing->getImports());
        $this->assertEmpty($ebay->getImports());
    }
    /**
     *
     */
    public function testGetComplexTypes()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagComplexType', $ebay->getComplexTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagComplexType', $bing->getComplexTypes());
    }
    /**
     *
     */
    public function testGetSimpleTypes()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagSimpleType', $ebay->getSimpleTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagSimpleType', $bing->getSimpleTypes());
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
        $ebay = self::eBayInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagHeader', $ebay->getHeaders());
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
        $ebay = self::eBayInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagDocumentation', $ebay->getDocumentations());
    }
    /**
     *
     */
    public function testGetExtensions()
    {
        $ebay = self::eBayInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagExtension', $ebay->getExtensions());
    }
    /**
     *
     */
    public function testGetLists()
    {
    }
    /**
     *
     */
    public function testGetUnions()
    {
    }
    /**
     *
     */
    public function testGetComplexContents()
    {
        $aukro = self::aukroInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagComplexContent', $aukro->getComplexContents());
    }
    /**
     *
     */
    public function testGetSimpleContents()
    {
    }
    /**
     *
     */
    public function testGetSequences()
    {
    }
    /**
     *
     */
    public function testGetAttributes()
    {
        $aukro = self::aukroInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\TagAttribute', $aukro->getAttributes());
    }
    public static function assertContainsOnlyInstancesOf($classname, $haystack, $message = '')
    {
        return parent::assertNotEmpty($haystack) && parent::assertContainsOnlyInstancesOf($classname, $haystack, $message);
    }
}
