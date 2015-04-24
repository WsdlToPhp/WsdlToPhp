<?php

namespace WsdlToPhp\PackageGenerator\Tests\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\Struct;

use WsdlToPhp\PackageGenerator\Model\EmptyModel;
use WsdlToPhp\PackageGenerator\ModelContainer\ModelContainer;
use WsdlToPhp\PackageGenerator\Tests\TestCase;

class ModelContainerTest extends TestCase
{
    /**
     *
     */
    public function testAdd()
    {
        $modelContainer = new ModelContainer();
        $modelContainer->add(new EmptyModel('Foo'));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnObject()
    {
        $modelContainer = new ModelContainer();
        $modelContainer->add(array());
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnModelClass()
    {
        $modelContainer = new ModelContainer();
        $modelContainer->add(new Struct('Foo'));
    }
    /**
     *
     */
    public function testGet()
    {
        $modelContainer = new ModelContainer();
        $modelContainer->add(new EmptyModel('Foo'));
        $modelContainer->add(new EmptyModel('Bar'));

        $this->assertInstanceOf('\\WsdlToPhp\\PackageGenerator\\Model\\EmptyModel', $modelContainer->get('Foo'));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetWithException()
    {

        $modelContainer = new ModelContainer();
        $modelContainer->add(new EmptyModel('Foo'));
        $modelContainer->add(new EmptyModel('Bar'));

        $modelContainer->get('Foo', 'bar');
    }
}
