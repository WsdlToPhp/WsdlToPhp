<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Generator\AbstractContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagSimpleType;

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
    public function testGetBingSimpleTypes()
    {
        $tagSimpleTypeParser = self::bingInstance();
        AbstractContainer::purgeAllCache();

        $tagSimpleTypeParser->parse();
    }
}
