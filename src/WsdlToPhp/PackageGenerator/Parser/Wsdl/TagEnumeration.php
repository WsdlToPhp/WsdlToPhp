<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagEnumeration as Enumeration;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\Struct;

class TagEnumeration extends AbstractTagParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagEnumeration]
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
            $this->parseEnumeration($tag);
        }
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_ENUMERATION;
    }
    /**
     * @param Enumeration $enumeration
     */
    protected function parseEnumeration(Enumeration $enumeration)
    {
        $parent = $enumeration->getSuitableParent();
        if ($parent !== null) {
            $this->addStructValue($parent, $enumeration);
        }
    }
    /**
     * @param Tag $tag
     * @param Enumeration $enumeration
     */
    public function addStructValue(Tag $tag, Enumeration $enumeration)
    {
        $struct = $this->getModel($tag);
        if ($struct instanceof  Struct) {
            $struct->addValue($enumeration->getValue());
        }
    }
}
