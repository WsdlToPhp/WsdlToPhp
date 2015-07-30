<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagEnumeration;
use WsdlToPhp\PackageGenerator\Model\Struct;
use WsdlToPhp\PackageGenerator\Container\Model\StructValue as StructValueContainer;
use WsdlToPhp\PackageGenerator\Model\StructValue;

class TagEnumerationTest extends WsdlParser
{
    /**
     * @return TagEnumeration
     */
    public static function bingInstance()
    {
        return new TagEnumeration(self::generatorInstance(self::wsdlBingPath()));
    }
    /**
     * @return TagEnumeration
     */
    public static function reformaInstance()
    {
        return new TagEnumeration(self::generatorInstance(self::wsdlReformaPath()));
    }
    /**
     *
     */
    public function testBing()
    {
        $tagEnumerationParser = self::bingInstance();

        $tagEnumerationParser->parse();

        $count = 0;
        foreach ($tagEnumerationParser->getGenerator()->getStructs() as $struct) {
            if ($struct instanceof Struct && $struct->getIsRestriction() === true) {
                if ($struct->getName() === 'AdultOption') {
                    $values = new StructValueContainer();
                    $values->add(new StructValue($tagEnumerationParser->getGenerator(), 'Off', 0, $struct));
                    $values->add(new StructValue($tagEnumerationParser->getGenerator(), 'Moderate', 1, $struct));
                    $values->add(new StructValue($tagEnumerationParser->getGenerator(), 'Strict', 2, $struct));

                    $struct->getValues()->rewind();
                    $this->assertEquals($values, $struct->getValues());
                    $count++;
                } elseif ($struct->getName() === 'SearchOption') {
                    $values = new StructValueContainer();
                    $values->add(new StructValue($tagEnumerationParser->getGenerator(), 'DisableLocationDetection', 0, $struct));
                    $values->add(new StructValue($tagEnumerationParser->getGenerator(), 'EnableHighlighting', 1, $struct));

                    $struct->getValues()->rewind();
                    $this->assertEquals($values, $struct->getValues());
                    $count++;
                }
            }
        }
        $this->assertSame(2, $count);
    }
    /**
     *
     */
    public function testReforma()
    {
        $tagEnumerationParser = self::reformaInstance();

        $tagEnumerationParser->parse();

        $count = 0;
        foreach ($tagEnumerationParser->getGenerator()->getStructs() as $struct) {
            if ($struct instanceof Struct && $struct->getIsRestriction() === true) {
                if ($struct->getName() === 'HouseStateEnum') {
                    $values = new StructValueContainer();
                    $one = new StructValue($tagEnumerationParser->getGenerator(), '1', 0, $struct);
                    $one->setMeta(array(
                        'label' =>'normal',
                        'description' =>'Исправный',
                    ));
                    $values->add($one);
                    $two = new StructValue($tagEnumerationParser->getGenerator(), '2', 1, $struct);
                    $two->setMeta(array(
                        'label' =>'warning',
                        'description' =>'Требующий капитального ремонта',
                    ));
                    $values->add($two);
                    $three = new StructValue($tagEnumerationParser->getGenerator(), '3', 2, $struct);
                    $three->setMeta(array(
                        'label' =>'alarm',
                        'description' =>'Аварийный',
                    ));
                    $values->add($three);
                    $four = new StructValue($tagEnumerationParser->getGenerator(), '4', 3, $struct);
                    $four->setMeta(array(
                        'label' =>'noinfo',
                        'description' =>'Нет данных',
                    ));
                    $values->add($four);
                    $this->assertEquals($values, $struct->getValues());
                    $count++;
                } elseif ($struct->getName() === 'HouseStageEnum') {
                    $values = new StructValueContainer();
                    $one = new StructValue($tagEnumerationParser->getGenerator(), '1', 0, $struct);
                    $one->setMeta(array(
                        'label' =>'exploited',
                        'description' =>'Эксплуатируемый',
                    ));
                    $values->add($one);
                    $two = new StructValue($tagEnumerationParser->getGenerator(), '2', 1, $struct);
                    $two->setMeta(array(
                        'label' =>'decommissioned',
                        'description' =>'Выведенный из эксплуатации',
                    ));
                    $values->add($two);
                    $three = new StructValue($tagEnumerationParser->getGenerator(), '3', 2, $struct);
                    $three->setMeta(array(
                        'label' =>'drifting',
                        'description' =>'Снесенный',
                    ));
                    $values->add($three);

                    $struct->getValues()->rewind();
                    $this->assertEquals($values, $struct->getValues());
                    $count++;
                }
            }
        }
        $this->assertSame(2, $count);
    }
}
