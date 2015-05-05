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
    public function testGetElementByNameFromWsdl()
    {
        $bing = self::bingInstance();

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagComplexType', $bing->getElementByName(Wsdl::TAG_COMPLEX_TYPE));
    }
    public static function assertContainsOnlyInstancesOf($classname, $haystack, $message = '')
    {
        return parent::assertNotEmpty($haystack) && parent::assertContainsOnlyInstancesOf($classname, $haystack, $message);
    }
}
