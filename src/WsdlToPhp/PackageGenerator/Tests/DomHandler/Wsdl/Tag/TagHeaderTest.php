<?php

namespace WsdlToPhp\PackageGenerator\Test\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Tests\Model\WsdlTest;

class TagHeaderTest extends TestCase
{
    /**
     *
     */
    public function testIsEnumeration()
    {
        $wsdl = WsdlTest::ebayInstance();

        $headers = $wsdl->getContent()->getHeaders();

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
}
