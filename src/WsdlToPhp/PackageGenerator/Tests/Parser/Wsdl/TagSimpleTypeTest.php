<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Generator\AbstractContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagSimpleType;
use WsdlToPhp\PackageGenerator\Model\Struct;
use WsdlToPhp\PackageGenerator\ModelContainer\StructValueContainer;
use WsdlToPhp\PackageGenerator\Model\StructValue;

class TagSimpleTypeTest extends WsdlParser
{
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagSimpleType
     */
    public static function bingInstance()
    {
        return new TagSimpleType(self::generatorInstance(self::wsdlBingPath()));
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagSimpleType
     */
    public static function partnerInstance()
    {
        return new TagSimpleType(self::generatorInstance(self::wsdlPartnerPath()));
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagSimpleType
     */
    public static function imageViewInstance()
    {
        return new TagSimpleType(self::generatorInstance(self::wsdlImageViewServicePath()));
    }
    /**
     *
     */
    public function testParseBing()
    {
        $tagSimpleTypeParser = self::bingInstance();
        AbstractContainer::purgeAllCache();

        $tagSimpleTypeParser->parse();

        $ok = false;
        foreach ($tagSimpleTypeParser->getGenerator()->getStructs() as $struct) {
            if ($struct instanceof Struct && $struct->getIsRestriction() === true) {
                if ($struct->getName() === 'AdultOption') {
                    $values = new StructValueContainer();
                    $values->add(new StructValue('Off', 0, $struct));
                    $values->add(new StructValue('Moderate', 1, $struct));
                    $values->add(new StructValue('Strict', 2, $struct));

                    $this->assertEquals($values, $struct->getValues());
                    $ok = true;
                } elseif ($struct->getName() === 'SearchOption') {
                    $values = new StructValueContainer();
                    $values->add(new StructValue('DisableLocationDetection', 0, $struct));
                    $values->add(new StructValue('EnableHighlighting', 1, $struct));

                    $this->assertEquals($values, $struct->getValues());
                    $ok = true;
                }
            }
        }
        $this->assertTrue((bool)$ok);
    }
    /**
     *
     */
    public function testParseImageViewService()
    {
        $tagSimpleTypeParser = self::imageViewInstance();
        AbstractContainer::purgeAllCache();

        $tagSimpleTypeParser->parse();

        $ok = false;
        foreach ($tagSimpleTypeParser->getGenerator()->getStructs() as $struct) {
            if ($struct instanceof Struct && $struct->getIsRestriction() === false) {
                if ($struct->getName() === 'EchoRequestType') {
                    $ok = true;
                } elseif ($struct->getName() === 'PasswordType') {
                    $ok = true;
                }
            }
        }
        $this->assertTrue((bool)$ok);
    }
}
