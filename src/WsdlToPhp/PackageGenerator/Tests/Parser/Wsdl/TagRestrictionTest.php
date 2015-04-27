<?php

namespace WsdlToPhp\PackageGenerator\Tests\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Generator\AbstractContainer;
use WsdlToPhp\PackageGenerator\Parser\Wsdl\TagRestriction;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

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
    /**
     *
     */
    public function testGetSuitableParentTags()
    {
        $tagRestrictionParser = self::imageViewInstance();
        AbstractContainer::purgeAllCache();

        $tagRestrictionParser->parse();

        $parentTags = array(
            WsdlDocument::TAG_SIMPLE_TYPE,
            WsdlDocument::TAG_SIMPLE_TYPE,
            WsdlDocument::TAG_SIMPLE_TYPE,
            WsdlDocument::TAG_SIMPLE_TYPE,
            WsdlDocument::TAG_SIMPLE_TYPE,
            WsdlDocument::TAG_SIMPLE_TYPE,
            WsdlDocument::TAG_ELEMENT,
            WsdlDocument::TAG_SIMPLE_TYPE,
        );
        foreach ($tagRestrictionParser->getRestrictions() as $index=>$restriction) {
            $this->assertSame($parentTags[$index], $restriction->getSuitableParent()->getName());
        }
    }
    /**
     *
     */
    public function testGetSuitableParentNames()
    {
        $tagRestrictionParser = self::imageViewInstance();
        AbstractContainer::purgeAllCache();

        $tagRestrictionParser->parse();

        $parentNames = array(
            'EchoRequestType',
            'PasswordType',
            'UserType',
            'DocumentType',
            'ErrorMessageType',
            'ProType',
            'requestID',
            'SearchItemType',
        );
        foreach ($tagRestrictionParser->getRestrictions() as $index=>$restriction) {
            $this->assertSame($parentNames[$index], $restriction->getSuitableParent()->getAttribute('name')->getValue());
        }
    }
}
