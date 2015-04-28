<?php

namespace WsdlToPhp\PackageGenerator\Test\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Tests\Model\WsdlTest;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl;

class TagRestrictionTest extends TestCase
{
    /**
     *
     */
    public function testIsEnumeration()
    {
        $wsdl = WsdlTest::bingInstance();

        $restrictions = $wsdl->getContent()->getRestrictions();

        foreach ($restrictions as $index=>$restriction) {
            $this->assertTrue($restriction->isEnumeration());
        }
    }
}
