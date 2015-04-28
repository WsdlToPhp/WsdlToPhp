<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Container\AbstractObjectContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagImport;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\Model\Wsdl;

class TagImportTest extends WsdlParser
{
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagImport
     */
    public static function instance()
    {
        return new TagImport(new Generator(self::wsdlPartnerPath()));
    }
    /**
     *
     */
    public function testIsWsdlParsed()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $this->assertTrue($tagImportParser->isWsdlParsed(new Wsdl(self::wsdlPartnerPath(), file_get_contents(self::wsdlPartnerPath()))));
        $this->assertTrue($tagImportParser->isWsdlParsed(new Wsdl(self::schemaPartnerPath(), file_get_contents(self::schemaPartnerPath()))));
    }
    /**
     *
     */
    public function testCountWsdlsAfterParsing()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $this->assertCount(20, $tagImportParser->getGenerator()->getWsdls());
    }
}
