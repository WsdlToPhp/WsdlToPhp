<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Generator\Utils;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\Model\EmptyModel;

class TagImport extends AbstractParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagImport]
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
            if ($tag->getLocationAttribute() != '') {
                $finalLocation = Utils::resolveCompletePath($wsdl->getName(), $tag->getLocationAttribute());
                $this->generator->addWsdl($finalLocation);
            }
        }
    }
	/**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     * @return string
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_IMPORT;
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getModel()
     * @return EmptyModel
     */
    protected function getModel(Tag $tag)
    {
        return new EmptyModel($tag->getAttributeName());
    }
}
