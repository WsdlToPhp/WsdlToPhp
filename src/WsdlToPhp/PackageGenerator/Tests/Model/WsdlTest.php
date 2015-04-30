<?php

namespace WsdlToPhp\PackageGenerator\Tests\Model;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Model\Wsdl;

class WsdlTest extends TestCase
{
    /**
     * @return Wsdl
     */
    public static function bingInstance()
    {
        return new Wsdl(dirname(__FILE__) . '/../resources/bingsearch.wsdl', file_get_contents(dirname(__FILE__) . '/../resources/bingsearch.wsdl'));
    }
    /**
     * @return Wsdl
     */
    public static function partnerInstance()
    {
        return new Wsdl(dirname(__FILE__) . '/../resources/PartnerService.wsdl', file_get_contents(dirname(__FILE__) . '/../resources/PartnerService.wsdl'));
    }
    /**
     * @return Wsdl
     */
    public static function imageServiceViewInstance()
    {
        return new Wsdl(dirname(__FILE__) . '/../resources/ImageViewService.local.wsdl', file_get_contents(dirname(__FILE__) . '/../resources/ImageViewService.local.wsdl'));
    }
    /**
     * @return Wsdl
     */
    public static function imageServiceViewAvailRequestInstance()
    {
        return new Wsdl(dirname(__FILE__) . '/../resources/availableImagesRequest.xsd', file_get_contents(dirname(__FILE__) . '/../resources/availableImagesRequest.xsd'));
    }
    /**
     *
     */
    public function testGetName()
    {
        $this->assertSame(dirname(__FILE__) . '/../resources/bingsearch.wsdl', self::bingInstance()->getName());
    }
}