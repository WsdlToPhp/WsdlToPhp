<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Container\AbstractContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagComplexType;
use WsdlToPhp\PackageGenerator\Model\Struct;

class TagComplexTypeTest extends WsdlParser
{
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagComplexType
     */
    public static function bingInstance()
    {
        return new TagComplexType(self::generatorInstance(self::wsdlBingPath()));
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagComplexType
     */
    public static function partnerInstance()
    {
        return new TagComplexType(self::generatorInstance(self::wsdlPartnerPath()));
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagComplexType
     */
    public static function imageViewInstance()
    {
        return new TagComplexType(self::generatorInstance(self::wsdlImageViewServicePath()));
    }
    /**
     *
     */
    public function testParseBing()
    {
        $tagComplexTypeParser = self::bingInstance();
        AbstractContainer::purgeAllCache();

        $tagComplexTypeParser->parse();

        $ok = false;
        foreach ($tagComplexTypeParser->getGenerator()->getStructs() as $struct) {
            if ($struct instanceof Struct && $struct->getIsStruct() === true) {
                if ($struct->getName() === 'SearchRequest') {
                    $ok = true;
                } elseif ($struct->getName() === 'ImageResult') {
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
        $tagComplexTypeParser = self::imageViewInstance();
        AbstractContainer::purgeAllCache();

        $tagComplexTypeParser->parse();

        $ok = false;
        foreach ($tagComplexTypeParser->getGenerator()->getStructs() as $struct) {
            if ($struct instanceof Struct && $struct->getIsStruct() === true) {
                if ($struct->getName() === 'imgRequest') {
                    $this->assertEquals(array(
                        'PRO is deprecated; provided for backward compatibility',
                    ), $struct->getMetaValue(Struct::META_DOCUMENTATION));
                    $ok = true;
                } elseif ($struct->getName() === 'ProType') {
                    $this->assertEquals(array(
                        'PRO is 10 digits or 11 digits with dash.',
                    ), $struct->getMetaValue(Struct::META_DOCUMENTATION));
                    $ok = true;

                } elseif ($struct->getName() === 'SearchCriteriaType') {
                    $this->assertEquals(array(
                        'Generic search criteria for image search',
                    ), $struct->getMetaValue(Struct::META_DOCUMENTATION));
                    $ok = true;
                } elseif ($struct->getName() === 'SearchItemType') {
                    $this->assertEquals(array(
                        'Image search item',
                    ), $struct->getMetaValue(Struct::META_DOCUMENTATION));
                    $ok = true;
                } elseif ($struct->getName() === 'DocumentType') {
                    $this->assertEquals(array(
                        'Document type code',
                    ), $struct->getMetaValue(Struct::META_DOCUMENTATION));
                    $ok = true;
                } elseif ($struct->getName() === 'ImagesType') {
                    $this->assertEquals(array(
                        'Image file name and Base64 encoded binary source data',
                    ), $struct->getMetaValue(Struct::META_DOCUMENTATION));
                    $ok = true;
                } elseif ($struct->getName() === 'availRequest') {
                    $this->assertEquals(array(
                        'PRO is deprecated; provided for backward compatibility',
                    ), $struct->getMetaValue(Struct::META_DOCUMENTATION));
                    $ok = true;
                }
            }
        }
        $this->assertTrue((bool)$ok);
    }
}
