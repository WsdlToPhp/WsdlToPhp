<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Generator\AbstractContainer;

use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagRestriction;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\Model\Wsdl;

class TagRestrictionTest extends WsdlParser
{
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagRestriction
     */
    public static function bingInstance()
    {
        return new TagRestriction(self::generatorInstance(self::wsdlBingPath()));
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagRestriction
     */
    public static function partnerInstance()
    {
        return new TagRestriction(self::generatorInstance(self::wsdlPartnerPath()));
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\TagRestriction
     */
    public static function imageViewInstance()
    {
        return new TagRestriction(self::generatorInstance(self::wsdlImageViewServicePath()));
    }
    /**
     *
     */
    public function testGetBingRestrictions()
    {
        $tagRestrictionParser = self::bingInstance();
        AbstractContainer::purgeAllCache();

        $tagRestrictionParser->parse();

        $this->assertCount(0, $tagRestrictionParser->getRestrictions());
    }
    /**
     *
     */
    public function testGetPartnerRestrictions()
    {
        $tagRestrictionParser = self::partnerInstance();
        AbstractContainer::purgeAllCache();

        $tagRestrictionParser->parse();

        $this->assertCount(3, $tagRestrictionParser->getRestrictions());
    }
    /**
     *
     */
    public function testGetImageViewRestrictions()
    {
        $tagRestrictionParser = self::imageViewInstance();
        AbstractContainer::purgeAllCache();

        $tagRestrictionParser->parse();

        $this->assertCount(8, $tagRestrictionParser->getRestrictions());
    }
}
