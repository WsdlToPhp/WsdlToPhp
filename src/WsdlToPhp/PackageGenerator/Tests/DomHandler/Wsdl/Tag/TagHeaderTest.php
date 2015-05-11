<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Tests\Model\WsdlTest;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl;

class TagHeaderTest extends TestCase
{
    /**
     *
     */
    public function testIsEnumeration()
    {
        $wsdl = WsdlTest::ebayInstance();

        $headers = $wsdl->getContent()->getElementsByName(Wsdl::TAG_HEADER);

        foreach ($headers as $index=>$header) {
            $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagOperation', $header->getParentOperation());
            if ($header->getParentInput() !== null) {
                $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagInput', $header->getParentInput());
            }
            $this->assertSame('RequesterCredentials', $header->getAttributePart());
            $this->assertSame('RequesterCredentials', $header->getAttributeMessage());
            $this->assertSame('', $header->getAttributeRequired());
            $this->assertSame('', $header->getAttributeNamespace());
        }
    }
    /**
     *
     */
    public function testGetMessage()
    {
        $wsdl = WsdlTest::ebayInstance();

        $header = $wsdl->getContent()->getElementByName(Wsdl::TAG_HEADER);

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagMessage', $header->getMessage());
    }
    /**
     *
     */
    public function testGetPart()
    {
        $wsdl = WsdlTest::ebayInstance();

        $header = $wsdl->getContent()->getElementByName(Wsdl::TAG_HEADER);

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagPart', $header->getPart());
    }
    /**
     *
     */
    public function testGetPartFinalType()
    {
        $wsdl = WsdlTest::ebayInstance();

        $header = $wsdl->getContent()->getElementByName(Wsdl::TAG_HEADER);

        $this->assertSame('CustomSecurityHeaderType', $header->getPart()->getFinalType());
    }
    /**
     *
     */
    public function testGetPartFinalNamespace()
    {
        $wsdl = WsdlTest::ebayInstance();

        $header = $wsdl->getContent()->getElementByName(Wsdl::TAG_HEADER);

        $this->assertSame('ns', $header->getPart()->getFinalNamespace());
    }
}
