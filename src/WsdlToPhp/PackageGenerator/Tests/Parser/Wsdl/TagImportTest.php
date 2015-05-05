<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Container\AbstractObjectContainer;
use WsdlToPhp\PackageGenerator\Container\Model\Schema as SchemaContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagImport;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\Schema;

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
    }
    /**
     *
     */
    public function testCountWsdlsAfterParsing()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $this->assertCount(1, $tagImportParser->getGenerator()->getWsdls());
    }
    /**
     *
     */
    public function testGetExternalSchemas()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $schemaContainer = new SchemaContainer();
        for ($i=0; $i<19; $i++) {
            $schemaPath = sprintf(dirname(__FILE__) . '/../../resources/PartnerService.%d.xsd', $i);
            $schema = new Schema($schemaPath, file_get_contents($schemaPath));
            $schema->getContent()->setCurrentTag('import');
            $schemaContainer->add($schema);
        }

        $tagImportParser->getGenerator()->getWsdl(0)->getContent()->getExternalSchemas()->rewind();
        $this->assertEquals($schemaContainer, $tagImportParser->getGenerator()->getWsdl(0)->getContent()->getExternalSchemas());
    }
}
