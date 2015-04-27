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
     *
     */
    public function testGetName()
    {
        $this->assertSame(dirname(__FILE__) . '/../resources/bingsearch.wsdl', self::bingInstance()->getName());
    }
}