<?php

namespace WsdlToPhp\PackageGenerator\Tests\Model;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\Model\Service;

class MethodTest extends TestCase
{
    /**
     *
     */
    public function testGetMethodName()
    {
        $service = new Service(self::getBingGeneratorInstance(), 'Foo');
        $service->addMethod('getId', 'string', 'string');
        $service->addMethod('getid', 'string', 'string');
        $service->addMethod('getIdString', 'string', 'id', false);
        $service->addMethod('getIdInt', 'int', 'id', false);

        $this->assertSame('getId', $service->getMethod('getId')->getMethodName());
        $this->assertSame('getid_1', $service->getMethod('getid')->getMethodName());
        $this->assertSame('getIdStringString', $service->getMethod('getIdString')->getMethodName());
        $this->assertSame('getIdIntInt', $service->getMethod('getIdInt')->getMethodName());
    }
    /**
     *
     */
    public function testGetMethodNameCalledTwice()
    {
        $service = new Service(self::getBingGeneratorInstance(), 'Foo');
        $service->addMethod('getId', 'string', 'string');
        $service->addMethod('get.id', 'string', 'string');
        $service->addMethod('getIdString', 'string', 'id', false);
        $service->addMethod('getIdInt', 'int', 'id', false);
        $service->addMethod('list', 'int', 'id', true);

        $method = $service->getMethod('get.id');
        $this->assertSame('get_id', $method->getMethodName());
        $this->assertSame('get_id', $method->getMethodName());

        $method = $service->getMethod('list');
        $this->assertSame('_list', $method->getMethodName());
        $this->assertSame('_list', $method->getMethodName());
    }
    /**
     *
     */
    public function testMultipleServicesSameMethods()
    {
        Service::purgeUniqueNames();
        $service1 = new Service(self::getBingGeneratorInstance(), 'Login');
        $service1->addMethod('Login', 'int', 'id');

        $service2 = new Service(self::getBingGeneratorInstance(), 'Login');
        $service2->addMethod('login', 'int', 'id');

        $service3 = new Service(self::getBingGeneratorInstance(), 'login');
        $service3->addMethod('Login', 'int', 'id');

        $this->assertSame('Login', $service1->getMethod('Login')->getMethodName());
        $this->assertSame('login_1', $service2->getMethod('login')->getMethodName());
        $this->assertSame('Login', $service3->getMethod('Login')->getMethodName());
    }
    /**
     *
     */
    public function testMultipleServicesSameMethodsWithoutPurging()
    {
        Service::purgeUniqueNames();
        $service1 = new Service(self::getBingGeneratorInstance(), 'Login');
        $service1->addMethod('Login', 'int', 'id');

        $service2 = new Service(self::getBingGeneratorInstance(), 'Login');
        $service2->addMethod('login', 'int', 'id');

        $service3 = new Service(self::getBingGeneratorInstance(), 'login');
        $service3->addMethod('Login', 'int', 'id');

        $this->assertSame('Login', $service1->getMethod('Login')->getMethodName());
        $this->assertSame('login_1', $service2->getMethod('login')->getMethodName());
        $this->assertSame('Login', $service3->getMethod('Login')->getMethodName());
    }
}
