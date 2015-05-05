<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Tests\Model\WsdlTest;

class TagMessageTest extends TestCase
{
    /**
     *
     */
    public function testGetPart()
    {
        $wsdl = WsdlTest::ebayInstance();

        $messages = $wsdl->getContent()->getMessages();

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\DomHandler\\Wsdl\\Tag\\TagPart', $messages[0]->getPart('RequesterCredentials'));
    }
}
