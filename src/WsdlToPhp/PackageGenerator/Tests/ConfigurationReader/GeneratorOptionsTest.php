<?php

namespace WsdlToPhp\PackageGenerator\Tests\ConfigurationReader;

use WsdlToPhp\PackageGenerator\Tests\TestCase;
use WsdlToPhp\PackageGenerator\ConfigurationReader\GeneratorOptions;

class GeneratorOptionsTest extends TestCase
{
    /**
     * @return GeneratorOptions
     */
    public static function optionsInstance()
    {
        $options = clone GeneratorOptions::instance(__DIR__ . '/../resources/generator_options.yml');
        return $options;
    }
    /**
     *
     */
    public function testGetPrefix()
    {
        $this->assertEmpty(self::optionsInstance()->getPrefix());
    }
    /**
     *
     */
    public function testSetPrefix()
    {
        $instance = self::optionsInstance();
        $instance->setPrefix('MyPrefix');

        $this->assertSame('MyPrefix', $instance->getPrefix());
    }
    /**
     *
     */
    public function testGetDestination()
    {
        $this->assertEmpty(self::optionsInstance()->getDestination());
    }
    /**
     *
     */
    public function testSetDestination()
    {
        $instance = self::optionsInstance();
        $instance->setDestination('/my/destination/');

        $this->assertSame('/my/destination/', $instance->getDestination());
    }
    /**
     *
     */
    public function testGetOrigin()
    {
        $this->assertEmpty(self::optionsInstance()->getOrigin());
    }
    /**
     *
     */
    public function testSetOrigin()
    {
        $instance = self::optionsInstance();
        $instance->setOrigin('/my/path/to/the/wsdl/file.wsdl');

        $this->assertSame('/my/path/to/the/wsdl/file.wsdl', $instance->getOrigin());
    }
    /**
     *
     */
    public function testGetBasicLogin()
    {
        $this->assertEmpty(self::optionsInstance()->getBasicLogin());
    }
    /**
     *
     */
    public function testSetBasicLogin()
    {
        $instance = self::optionsInstance();
        $instance->setBasicLogin('MyLogin');

        $this->assertSame('MyLogin', $instance->getBasicLogin());
    }
    /**
     *
     */
    public function testGetBasicPassword()
    {
        $this->assertEmpty(self::optionsInstance()->getBasicPassword());
    }
    /**
     *
     */
    public function testSetBasicPassword()
    {
        $instance = self::optionsInstance();
        $instance->setBasicPassword('MyPassword');

        $this->assertSame('MyPassword', $instance->getBasicPassword());
    }
    /**
     *
     */
    public function testGetProxyHost()
    {
        $this->assertEmpty(self::optionsInstance()->getProxyHost());
    }
    /**
     *
     */
    public function testSetProxyHost()
    {
        $instance = self::optionsInstance();
        $instance->setProxyHost('MyProxyHost');

        $this->assertSame('MyProxyHost', $instance->getProxyHost());
    }
    /**
     *
     */
    public function testGetProxyPort()
    {
        $this->assertEmpty(self::optionsInstance()->getProxyPort());
    }
    /**
     *
     */
    public function testSetProxyPort()
    {
        $instance = self::optionsInstance();
        $instance->setProxyPort(3225);

        $this->assertSame(3225, $instance->getProxyPort());
    }
    /**
     *
     */
    public function testGetProxyLogin()
    {
        $this->assertEmpty(self::optionsInstance()->getProxyLogin());
    }
    /**
     *
     */
    public function testSetProxyLogin()
    {
        $instance = self::optionsInstance();
        $instance->setProxyLogin('MyProxyLogin');

        $this->assertSame('MyProxyLogin', $instance->getProxyLogin());
    }
    /**
     *
     */
    public function testGetProxyPassword()
    {
        $this->assertEmpty(self::optionsInstance()->getProxyPassword());
    }
    /**
     *
     */
    public function testSetProxyPassword()
    {
        $instance = self::optionsInstance();
        $instance->setProxyPassword('MyProxyPassword');

        $this->assertSame('MyProxyPassword', $instance->getProxyPassword());
    }
    /**
     *
     */
    public function testGetCategory()
    {
        $this->assertSame(GeneratorOptions::VALUE_CAT, self::optionsInstance()->getCategory());
    }
    /**
     *
     */
    public function testSetCategory()
    {
        $instance = self::optionsInstance();
        $instance->setCategory(GeneratorOptions::VALUE_NONE);

        $this->assertSame(GeneratorOptions::VALUE_NONE, $instance->getCategory());
    }
    /**
     *
     */
    public function testGetGatherMethods()
    {
        $this->assertSame(GeneratorOptions::VALUE_START, self::optionsInstance()->getGatherMethods());
    }
    /**
     *
     */
    public function testSetGatherMethods()
    {
        $instance = self::optionsInstance();
        $instance->setGatherMethods(GeneratorOptions::VALUE_END);

        $this->assertSame(GeneratorOptions::VALUE_END, $instance->getGatherMethods());
    }
    /**
     *
     */
    public function testGetGenericConstantsName()
    {
        $this->assertFalse(self::optionsInstance()->getGenericConstantsName());
    }
    /**
     *
     */
    public function testSetGenericConstantsName()
    {
        $instance = self::optionsInstance();
        $instance->setGenericConstantsName(GeneratorOptions::VALUE_TRUE);

        $this->assertTrue($instance->getGenericConstantsName());
    }
    /**
     *
     */
    public function testGetGenerateTutorialFile()
    {
        $this->assertTrue(self::optionsInstance()->getGenerateTutorialFile());
    }
    /**
     *
     */
    public function testSetGenerateTutorialFile()
    {
        $instance = self::optionsInstance();
        $instance->setGenerateTutorialFile(GeneratorOptions::VALUE_FALSE);

        $this->assertFalse($instance->getGenerateTutorialFile());
    }
    /**
     *
     */
    public function testGetAddComments()
    {
        $comments = array(
            'release' => '1.0.2',
            'date' => '2015-09-08',
        );

        $instance = self::optionsInstance();
        $instance->setAddComments($comments);

        $this->assertSame($comments, $instance->getAddComments());
    }
    /**
     *
     */
    public function testGetNamespace()
    {
        $this->assertSame('', self::optionsInstance()->getNamespace());
    }
    /**
     *
     */
    public function testSetNamespace()
    {
        $instance = self::optionsInstance();
        $instance->setNamespace('\\My\\Project');

        $this->assertSame('\\My\\Project', $instance->getNamespace());
    }
    /**
     *
     */
    public function testGetStandalone()
    {
        $this->assertTrue(self::optionsInstance()->getStandalone());
    }
    /**
     *
     */
    public function testSetStandalone()
    {
        $instance = self::optionsInstance();
        $instance->setStandalone(GeneratorOptions::VALUE_FALSE);

        $this->assertFalse($instance->getStandalone());
    }
    /**
     *
     */
    public function testGetStructClass()
    {
        $this->assertSame('\\WsdlToPhp\\PackageBase\\AbstractStructBase', self::optionsInstance()->getStructClass());
    }
    /**
     *
     */
    public function testSetStructClass()
    {
        $instance = self::optionsInstance();
        $instance->setStructClass('\\My\\Project\\StructClass');

        $this->assertSame('\\My\\Project\\StructClass', $instance->getStructClass());
    }
    /**
     *
     */
    public function testGetStructArrayClass()
    {
        $this->assertSame('\\WsdlToPhp\\PackageBase\\AbstractStructArrayBase', self::optionsInstance()->getStructArrayClass());
    }
    /**
     *
     */
    public function testSetStructArrayClass()
    {
        $instance = self::optionsInstance();
        $instance->setStructArrayClass('\\My\\Project\\StructArrayClass');

        $this->assertSame('\\My\\Project\\StructArrayClass', $instance->getStructArrayClass());
    }
    /**
     *
     */
    public function testGetSoapClientClass()
    {
        $this->assertSame('\\WsdlToPhp\\PackageBase\\AbstractSoapClientBase', self::optionsInstance()->getSoapClientClass());
    }
    /**
     *
     */
    public function testSetSoapClientClass()
    {
        $instance = self::optionsInstance();
        $instance->setSoapClientClass('\\My\\Project\\SoapClientClass');

        $this->assertSame('\\My\\Project\\SoapClientClass', $instance->getSoapClientClass());
    }
    /**
     *
     */
    public function testSetExistingOptionValue()
    {
        $instance = self::optionsInstance();
        $instance->setOptionValue('category', 'cat');
        $this->assertEquals('cat', $instance->getOptionValue('category'));
        $instance->setCategory('none');
        $this->assertEquals('none', $instance->getOptionValue('category'));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetExistingOptionValueWithInvalidValue()
    {
        self::optionsInstance()->setOptionValue('category', 'null');
        self::optionsInstance()->setCategory(null);
    }
    /**
     *
     */
    public function testSetUnexistingOptionValue()
    {
        $newOptionKey = 'new_option';
        $instance = self::optionsInstance();

        $instance->setOptionValue($newOptionKey, '1', array(0, 1, 2));

        $this->assertEquals(1, $instance->getOptionValue($newOptionKey));
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetUnexistingOptionValueWithInvalidValue()
    {
        self::optionsInstance()->setGenerateTutorialFile('null');
    }
}
