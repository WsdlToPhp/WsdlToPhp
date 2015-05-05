<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Container\AbstractObjectContainer;
use WsdlToPhp\PackageGenerator\Container\Model\Schema as SchemaContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagImport;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\Schema;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

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
    /**
     *
     */
    public function testGetRestrictionFromExternalSchemas()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagRestriction', $tagImportParser->getGenerator()->getWsdl(0)->getContent()->getElementByName(WsdlDocument::TAG_RESTRICTION, true));
    }
    /**
     *
     */
    public function testGetEnumerationByAttributesFromExternalSchemas()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagEnumeration', $tagImportParser->getGenerator()->getWsdl(0)->getContent()->getElementByNameAndAttributes(WsdlDocument::TAG_ENUMERATION, array(
            'value' => 'InternalServerError',
        ), true));
    }
    /**
     *
     */
    public function testGetElementsFromExternalSchemas()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();
        $restrictions = $tagImportParser->getGenerator()->getWsdl(0)->getContent()->getElementsByName(WsdlDocument::TAG_RESTRICTION, true);

        $this->assertNotEmpty($restrictions);
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagRestriction', $restrictions);
    }
    /**
     *
     */
    public function testGetElementsByAttributeFromExternalSchemas()
    {
        $tagImportParser = self::instance();
        AbstractObjectContainer::purgeAllCache();

        $tagImportParser->parse();
        $elements = $tagImportParser->getGenerator()->getWsdl(0)->getContent()->getElementsByNameAndAttributes(WsdlDocument::TAG_ELEMENT, array(
            'name' => 'PartnerCredentials',
        ), true);

        $this->assertNotEmpty($elements);
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagElement', $elements);
    }
}
