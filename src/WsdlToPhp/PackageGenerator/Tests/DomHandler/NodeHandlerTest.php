<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler;

use WsdlToPhp\PackageGenerator\Tests\TestCase;

class NodeHandlerTest extends TestCase
{
    /**
     *
     */
    public function testGetName()
    {
        $domDocument = DomDocumentHandlerTest::bingInstance();

        // first element tag
        $element = $domDocument->getNodeByName('element');

        $this->assertEquals('element', $element->getName());
        $this->assertEquals('definitions', $domDocument->getRootNode()->getName());
    }
    /**
     *
     */
    public function testGetNamespace()
    {
        $domDocument = DomDocumentHandlerTest::bingInstance();

        // first element tag
        $element = $domDocument->getNodeByName('element');

        $this->assertEquals('xsd', $element->getNamespace());
        $this->assertEquals('wsdl', $domDocument->getRootNode()->getNamespace());
    }
    /**
     *
     */
    public function testHasAttributes()
    {
        $domDocument = DomDocumentHandlerTest::bingInstance();

        // first schema tag
        $schema = $domDocument->getNodeByName('schema');
        // first sequence tag
        $sequence = $domDocument->getNodeByName('sequence');

        $this->assertTrue($schema->hasAttributes());
        $this->assertFalse($sequence->hasAttributes());
    }
    /**
     *
     */
    public function testHasChildren()
    {
        $domDocument = DomDocumentHandlerTest::bingInstance();

        // first schema tag
        $schema = $domDocument->getNodeByName('schema');
        // first element tag
        $element = $domDocument->getNodeByName('element');

        $this->assertTrue($schema->hasChildren());
        $this->assertFalse($element->hasChildren());
    }
    /**
     *
     */
    public function testGetChildren()
    {
        $domDocument = DomDocumentHandlerTest::bingInstance();

        // first schema tag
        $schema = $domDocument->getNodeByName('schema');
        // first element tag
        $element = $domDocument->getNodeByName('element');

        $this->assertNotEmpty($schema->getChildren());
        $this->assertContainsOnlyInstancesOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\AbstractNodeHandler', $schema->getChildren());
        $this->assertEmpty($element->getChildren());
    }
}
