<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Container\AbstractObjectContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagInclude;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\Model\Wsdl;

class TagIncludeTest extends WsdlParser
{
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagInclude
     */
    public static function instance()
    {
        return new TagInclude(new Generator(self::wsdlImageViewServicePath()));
    }
    /**
     *
     */
    public function testIsWsdlParsed()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $this->assertTrue($tagImportParser->isWsdlParsed(new Wsdl(self::wsdlImageViewServicePath(), file_get_contents(self::wsdlImageViewServicePath()))));
        $this->assertTrue($tagImportParser->isWsdlParsed(new Wsdl(self::schemaImageViewServicePath(), file_get_contents(self::schemaImageViewServicePath()))));
    }
    /**
     *
     */
    public function testCountWsdlsAfterParsing()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $this->assertCount(6, $tagImportParser->getGenerator()->getWsdls());
    }
}
