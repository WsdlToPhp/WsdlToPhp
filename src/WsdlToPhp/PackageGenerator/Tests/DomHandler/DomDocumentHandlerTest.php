<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler;

use WsdlToPhp\PackageGenerator\Tests\TestCase;

class DomDocumentHandlerTest extends TestCase
{
    /**
     * @return DomDocumentHandler
     */
    public static function instance()
    {
        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->load(dirname(__FILE__) . '/../resources/ebaySvc.wsdl');
        return new DomDocumentHandler($doc);
    }
    /**
     *
     */
    public function testGetNodeByName()
    {
        $instance = self::instance();
        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\Tests\\DomHandler\\NodeHandler', $instance->getNodeByName('wsdl:definitions'));
    }
}
