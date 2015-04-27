<?php

namespace WsdlToPhp\PackageGenerator\WsdlParser;

use WsdlToPhp\PackageGenerator\Model\Wsdl;

class TagInclude extends AbstractParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\WsdlParser\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagInclude]
     */
    protected function getTags()
    {
        return parent::getTags();
    }
	/**
     * @see \WsdlToPhp\PackageGenerator\WsdlParser\AbstractParser::parseWsdl()
     */
    protected function parseWsdl(Wsdl $wsdl)
    {
        foreach ($this->getTags() as $tag) {
            if ($tag->getLocationAttribute() != '') {

            }
        }
    }
	/**
     * @see \WsdlToPhp\PackageGenerator\WsdlParser\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
    }
}
