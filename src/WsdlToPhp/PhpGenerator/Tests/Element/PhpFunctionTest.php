<?php

namespace WsdlToPhp\PhpGenerator\Tests\Element;

use WsdlToPhp\PhpGenerator\Element\PhpVariable;
use WsdlToPhp\PhpGenerator\Element\PhpProperty;
use WsdlToPhp\PhpGenerator\Element\PhpFunctionParameter;
use WsdlToPhp\PhpGenerator\Element\PhpFunction;
use WsdlToPhp\PhpGenerator\Tests\TestCase;

class PhpFunctionTest extends TestCase
{
    public function testGetPhpDeclaration()
    {
        $function = new PhpFunction('foo', array(
            'bar',
            array(
                'name' => 'demo',
                'value' => 1,
            ),
            array(
                'name' => 'sample',
                'value' => null,
            ),
            new PhpFunctionParameter('deamon', true),
        ));

        $this->assertSame('function foo($bar, $demo = 1, $sample = null, $deamon = true)', $function->getPhpDeclaration());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddChild()
    {
        $function = new PhpFunction('foo', array());

        $function->addChild(new PhpProperty('Bar'));
    }

    public function testAddChildVariable()
    {
        $function = new PhpFunction('foo', array());

        $function->addChild(new PhpVariable('bar'));

        $this->assertCount(1, $function->getChildren());
    }

    public function testAddChildString()
    {
        $function = new PhpFunction('foo', array());

        $function->addChild('bar');

        $this->assertCount(1, $function->getChildren());
    }

    public function testToStringEmptyBody()
    {
        $function = new PhpFunction('foo', array(
            'bar',
            array(
                'name' => 'demo',
                'value' => 1,
            ),
            array(
                'name' => 'sample',
                'value' => null,
            ),
            new PhpFunctionParameter('deamon', true),
        ));

        $this->assertSame("function foo(\$bar, \$demo = 1, \$sample = null, \$deamon = true)\n{\n}", $function->toString());
    }

    public function testToStringWithBody()
    {
        $function = new PhpFunction('foo', array(
            'bar',
            array(
                'name' => 'demo',
                'value' => 1,
            ),
            array(
                'name' => 'sample',
                'value' => null,
            ),
            new PhpFunctionParameter('deamon', true),
        ));

        $function
            ->addChild(new PhpVariable('bar', 1))
            ->addChild('return $bar;');

        $this->assertSame("function foo(\$bar, \$demo = 1, \$sample = null, \$deamon = true)\n{\n    \$bar = 1;\n    return \$bar;\n}", $function->toString());
    }
}
