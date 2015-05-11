<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Container\AbstractObjectContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagHeader;

class TagHeaderTest extends WsdlParser
{
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagHeader
     */
    public static function imageViewServiceInstance()
    {
        return new TagHeader(self::generatorInstance(self::wsdlImageViewServicePath()));
    }
    /**
     *
     */
    public function testParseImageViewService()
    {
        $tagHeaderParser = self::imageViewServiceInstance();
        AbstractObjectContainer::purgeAllCache();

        $tagHeaderParser->parse();

        $ok = false;
        $services = $tagHeaderParser->getGenerator()->getServices();
        if ($services->count() > 0) {
            foreach ($services as $service) {
                if ($service->getName() === 'Image') {
                    foreach ($service->getMethods() as $method) {
                        $this->assertSame(array(
                            TagHeader::META_SOAP_HEADER_NAMES => array(
                                'auth',
                            ),
                            TagHeader::META_SOAP_HEADER_NAMESPACES => array(
                                'http://ws.estesexpress.com/imageview',
                            ),
                            TagHeader::META_SOAP_HEADER_TYPES => array(
                                'AuthenticationType',
                            ),
                            TagHeader::META_SOAP_HEADERS => array(
                                'optional',
                            ),
                        ), $method->getMeta());
                        $ok = true;
                    }
                }
            }
        }
        $this->assertTrue((bool)$ok);
    }
}
