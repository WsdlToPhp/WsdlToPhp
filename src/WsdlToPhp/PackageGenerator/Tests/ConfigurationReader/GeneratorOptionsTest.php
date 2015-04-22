<?php

namespace WsdlToPhp\PackageGenerator\Tests\ConfigurationReader;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\ConfigurationReader\GeneratorOptions;

class GeneratorOptionsTest extends TestCase
{
    /**
     * @return \WsdlToPhp\Generator\GeneratorOptions
     */
    public static function optionsInstance()
    {
        return GeneratorOptions::instance(dirname(__FILE__) . '/../resources/generator_options.yml');
    }

    public function testGetDefaultOptionValue()
    {
        $this->assertEquals('start', self::optionsInstance()->getOptionValue('category'));
        $this->assertEquals('start', self::optionsInstance()->getOptionValue('sub_category'));
        $this->assertEquals('start', self::optionsInstance()->getOptionValue('gather_methods'));
        $this->assertFalse(self::optionsInstance()->getOptionValue('send_array_as_parameter'));
        $this->assertFalse(self::optionsInstance()->getOptionValue('generate_autoload_file'));
        $this->assertTrue(self::optionsInstance()->getOptionValue('generate_wsdl_class'));
        $this->assertFalse(self::optionsInstance()->getOptionValue('response_as_wsdl_object'));
        $this->assertFalse(self::optionsInstance()->getOptionValue('send_parameters_as_array'));
        $this->assertTrue(self::optionsInstance()->getOptionValue('inherits_from_identifier'));
        $this->assertFalse(self::optionsInstance()->getOptionValue('generic_constants_names'));
        $this->assertTrue(self::optionsInstance()->getOptionValue('generate_tutorial_file'));
        $this->assertEquals(array(), self::optionsInstance()->getOptionValue('add_comments'));
    }

    public function testSetExistingOptionValue()
    {
        self::optionsInstance()->setOptionValue('category', 'end');
        $this->assertEquals('end', self::optionsInstance()->getOptionValue('category'));
        self::optionsInstance()->setCategory('start');
        $this->assertEquals('start', self::optionsInstance()->getOptionValue('category'));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetExistingOptionValueWithInvalidValue()
    {
        self::optionsInstance()->setOptionValue('category', 'null');
        self::optionsInstance()->setCategory(null);
    }

    public function testSetUnexistingOptionValue()
    {
        $newOptionKey = 'new_option';

        self::optionsInstance()->setOptionValue($newOptionKey, '1', array(0, 1, 2));

        $this->assertEquals(1, self::optionsInstance()->getOptionValue($newOptionKey));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetUnexistingOptionValueWithInvalidValue()
    {
        $newOptionKey = 'new_option';

        self::optionsInstance()->setOptionValue($newOptionKey, '1', array(0, 1, 2));

        $this->assertEquals(1, self::optionsInstance()->getOptionValue('new_option'));

        self::optionsInstance()->setOptionValue($newOptionKey, 'null');
    }
}
