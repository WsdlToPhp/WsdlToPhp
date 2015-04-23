<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler;

use WsdlToPhp\PackageGenerator\Tests\TestCase;

class ElementHandlerTest extends TestCase
{
    /**
     *
     */
    public function testHasAttribute()
    {
        $domDocument = DomDocumentHandlerTest::bingInstance();

        // first element tag
        $element = $domDocument->getElementByName('element');
        // first schema tag
        $schema = $domDocument->getElementByName('schema');

        $this->assertTrue($element->hasAttribute('minOccurs'));
        $this->assertTrue($element->hasAttribute('type'));
        $this->assertFalse($element->hasAttribute('minoccurs'));
        $this->assertTrue($schema->hasAttribute('targetNamespace'));
        $this->assertFalse($schema->hasAttribute('targetnamespace'));
    }
    /**
     *
     */
    public function testGetAttribute()
    {
        $domDocument = DomDocumentHandlerTest::bingInstance();

        // first element tag
        $element = $domDocument->getElementByName('element');
        // first schema tag
        $schema = $domDocument->getElementByName('schema');

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\AttributeHandler', $schema->getAttribute('elementFormDefault'));
        $this->assertEmpty($schema->getAttribute('targetnamespace'));
        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\AttributeHandler', $element->getAttribute('name'));
        $this->assertEmpty($schema->getAttribute('foo'));
    }
}
