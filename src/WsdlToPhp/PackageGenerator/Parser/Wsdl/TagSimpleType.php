<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

class TagSimpleType extends AbstractParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagSimpleType]
     */
    public function getTags()
    {
        return parent::getTags();
    }
	/**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parseWsdl()
     */
    protected function parseWsdl(Wsdl $wsdl)
    {
        foreach ($this->getTags() as $tag) {
            $this->parseRestrictions($tag);
            $this->parseAnnotations($tag);
        }
    }
	/**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_SIMPLE_CONTENT;
    }
}
