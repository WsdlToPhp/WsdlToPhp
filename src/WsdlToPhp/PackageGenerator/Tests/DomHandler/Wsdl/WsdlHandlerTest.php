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

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagImport', $partner->getImports());
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

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagComplexType', $ebay->getComplexTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagComplexType', $bing->getComplexTypes());
    }
    /**
     *
     */
    public function testGetSimpleTypes()
    {
        $ebay = self::eBayInstance();
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagSimpleType', $ebay->getSimpleTypes());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagSimpleType', $bing->getSimpleTypes());
    }
    /**
     *
     */
    public function testGetElements()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagElement', $bing->getElements());
    }
    /**
     *
     */
    public function testGetRestrictions()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagRestriction', $bing->getRestrictions());
    }
    /**
     *
     */
    public function testGetEnumerations()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagEnumeration', $bing->getEnumerations());
    }
    /**
     *
     */
    public function testGetInputs()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagInput', $bing->getInputs());
    }
    /**
     *
     */
    public function testGetOutputs()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagOutput', $bing->getOutputs());
    }
    /**
     *
     */
    public function testGetBodies()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagBody', $bing->getBodies());
    }
    /**
     *
     */
    public function testGetHeaders()
    {
        $ebay = self::eBayInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagHeader', $ebay->getHeaders());
    }
    /**
     *
     */
    public function testGetMessages()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagMessage', $bing->getMessages());
    }
    /**
     *
     */
    public function testGetParts()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagPart', $bing->getParts());
    }
    /**
     *
     */
    public function testGetOperations()
    {
        $bing = self::bingInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagOperation', $bing->getOperations());
    }
    /**
     *
     */
    public function testGetDocumentations()
    {
        $ebay = self::eBayInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagDocumentation', $ebay->getDocumentations());
    }
    /**
     *
     */
    public function testGetExtensions()
    {
        $ebay = self::eBayInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagExtension', $ebay->getExtensions());
    }
    /**
     *
     */
    public function testGetLists()
    {
        $aukro = self::aukroInstance();

        $aukro->getLists();
    }
    /**
     *
     */
    public function testGetUnions()
    {
        $aukro = self::aukroInstance();

        $aukro->getUnions();
    }
    /**
     *
     */
    public function testGetComplexContents()
    {
        $aukro = self::aukroInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagComplexContent', $aukro->getComplexContents());
    }
    /**
     *
     */
    public function testGetSimpleContents()
    {

        $aukro = self::aukroInstance();

        $aukro->getSimpleContents();
    }
    /**
     *
     */
    public function testGetSequences()
    {
        $aukro = self::aukroInstance();

        $aukro->getSequences();
    }
    /**
     *
     */
    public function testGetAttributes()
    {
        $aukro = self::aukroInstance();

        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagAttribute', $aukro->getAttributes());
    }
    /**
     *
     */
    public function testGetAlls()
    {
        $aukro = self::aukroInstance();

        $aukro->getAlls();
    }
    /**
     *
     */
    public function testGetAnnotations()
    {
        $aukro = self::aukroInstance();

        $aukro->getAnnotations();
    }
    /**
     *
     */
    public function testGetAnys()
    {
        $aukro = self::aukroInstance();

        $aukro->getAnys();
    }
    /**
     *
     */
    public function testGetAppinfos()
    {
        $aukro = self::aukroInstance();

        $aukro->getAppinfos();
    }
    /**
     *
     */
    public function testGetAnyAttributes()
    {
        $aukro = self::aukroInstance();

        $aukro->getAnyAttributes();
    }
    /**
     *
     */
    public function testGetAttributeGroups()
    {

        $aukro = self::aukroInstance();

        $aukro->getAttributeGroups();
    }
    /**
     *
     */
    public function testGetChoices()
    {
        $aukro = self::aukroInstance();

        $aukro->getChoices();
    }
    /**
     *
     */
    public function testGetFields()
    {
        $aukro = self::aukroInstance();

        $aukro->getFields();
    }
    /**
     *
     */
    public function testGetGroups()
    {
        $aukro = self::aukroInstance();

        $aukro->getGroups();
    }
    /**
     *
     */
    public function testGetKeys()
    {
        $aukro = self::aukroInstance();

        $aukro->getKeys();
    }
    /**
     *
     */
    public function testGetNotations()
    {
        $aukro = self::aukroInstance();

        $aukro->getNotations();
    }
    /**
     *
     */
    public function testGetSchemas()
    {
        $aukro = self::aukroInstance();

        $aukro->getSchemas();
    }
    /**
     *
     */
    public function testGetSelectors()
    {
        $aukro = self::aukroInstance();

        $aukro->getSelectors();
    }
    /**
     *
     */
    public function testGetUniques()
    {
        $aukro = self::aukroInstance();

        $aukro->getUniques();
    }
    /**
     *
     */
    public function testGetPortTypes()
    {
        $aukro = self::aukroInstance();

        $aukro->getPortTypes();
    }
    /**
     *
     */
    public function testGetBindings()
    {
        $aukro = self::aukroInstance();

        $aukro->getBindings();
    }
    /**
     *
     */
    public function testGetPorts()
    {
        $aukro = self::aukroInstance();

        $aukro->getPorts();
    }
    /**
     *
     */
    public function testGetAddresses()
    {
        $aukro = self::aukroInstance();

        $aukro->getAddresses();
    }
    /**
     *
     */
    public function testGetTypes()
    {
        $aukro = self::aukroInstance();

        $aukro->getTypes();
    }
    public static function assertContainsOnlyInstancesOf($classname, $haystack, $message = '')
    {
        return parent::assertNotEmpty($haystack) && parent::assertContainsOnlyInstancesOf($classname, $haystack, $message);
    }
}
