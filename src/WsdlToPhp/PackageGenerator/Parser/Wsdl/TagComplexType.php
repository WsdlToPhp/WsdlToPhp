<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagComplexType as ComplexType;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\Schema;

class TagComplexType extends AbstractTagParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagComplexType]
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
            $this->parseComplexType($tag);
        }
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parseSchema()
     */
    protected function parseSchema(Wsdl $wsdl, Schema $schema)
    {
        $this->parseWsdl($wsdl);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_COMPLEX_TYPE;
    }
    /**
     * @param ComplexType $complexType
     */
    protected function parseComplexType(ComplexType $complexType)
    {
        $model = $this->getModel($complexType);
        if ($model !== null && $complexType->hasAttributes()) {
            foreach ($complexType->getAttributes() as $attribute) {
                switch ($attribute->getName()) {
                    case AbstractAttributeHandler::ATTRIBUTE_NAME:
                        /**
                         * Avoid this attribute to be added as meta
                         */
                        break;
                    case AbstractAttributeHandler::ATTRIBUTE_ABSTRACT:
                        $model->setIsAbstract($attribute->getValue(false, true, 'bool'));
                        break;
                    default:
                        $model->addMeta($attribute->getName(), $attribute->getValue());
                        break;
                }
            }
        }
    }
}
