<?php

namespace WsdlToPhp\PackageGenerator\Tests\ModelContainer;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\ModelContainer\WsdlContainer;

class WsdlContainerTest extends TestCase
{
    const
        WSDL_BING = 'http://api.bing.net/search.wsdl',
        WSDL_OVH  = 'http://www.ovh.com/soapi/soapi-dlw-1.49.wsdl';
    /**
     * @return WsdlContainer
     */
    public static function instance()
    {
        $wsdlContainer = new WsdlContainer();
        $wsdlContainer->add(new Wsdl(self::WSDL_BING));
        $wsdlContainer->add(new Wsdl(self::WSDL_OVH));
        return $wsdlContainer;
    }
    /**
     *
     */
    public function testGetWsdlByName()
    {
        $wsdlContainer = self::instance();

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\Model\\Wsdl', $wsdlContainer->getWsdlByName(self::WSDL_BING));
        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\Model\\Wsdl', $wsdlContainer->getWsdlByName(self::WSDL_OVH));
        $this->assertNull($wsdlContainer->getWsdlByName('Bar'));
    }
}
