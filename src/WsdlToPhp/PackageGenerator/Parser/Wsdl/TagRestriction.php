<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

class TagRestriction extends AbstractParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagRestriction]
     */
    public function getTags()
    {
        return parent::getTags();
    }
    /**
     * @var array
     */
    protected $restrictions = array();
	/**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parseWsdl()
     */
    protected function parseWsdl(Wsdl $wsdl)
    {
        $this->initRestrictions();
    }
	/**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_RESTRICTION;
    }
    /**
     *
     */
    protected function initRestrictions()
    {
        foreach ($this->getTags() as $tag) {
            $enumerations = $tag->getChildrenByName(WsdlDocument::TAG_ENUMERATION);
            if (count($enumerations) === 0) {
                $this->restrictions[] = $tag;
            }
        }
    }
    /**
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagRestriction]
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }
}
