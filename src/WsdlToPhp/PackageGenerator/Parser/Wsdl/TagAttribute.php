<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAttribute as Attribute;
use WsdlToPhp\PackageGenerator\Model\Wsdl;

class TagAttribute extends AbstractTagParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAttribute]
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
            $this->parseAttribute($tag);
        }
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_ATTRIBUTE;
    }
    /**
     * @param Attribute $attribute
     */
    public function parseAttribute(Attribute $attribute)
    {
        $parent = $attribute->getSuitableParent();
        if ($parent !== null && $attribute->hasAttributeName() && ($model = $this->getModel($parent)) !== null && ($modelAttribute = $model->getAttribute($attribute->getAttributeName())) !== null) {
            if ($modelAttribute !== null) {
                foreach ($attribute->getAttributes() as $attributeAttribute) {
                    switch ($attributeAttribute->getName()) {
                        case AbstractAttributeHandler::ATTRIBUTE_NAME:
                            /**
                             * Avoid this attribute to be added as meta
                             */
                            break;
                        case AbstractAttributeHandler::ATTRIBUTE_TYPE:
                            $type = $attributeAttribute->getValue();
                            if ($type !== null) {
                                $typeModel = $this->generator->getStruct($type);
                                $modelAttributeType = $modelAttribute->getType();
                                if ($typeModel !== null && (empty($modelAttributeType) || strtolower($modelAttributeType) === 'unknown')) {
                                    if ($typeModel->getIsRestriction()) {
                                        $modelAttribute->setType($typeModel->getName());
                                    } elseif (!$typeModel->getIsStruct() && $typeModel->getInheritance()) {
                                        $modelAttribute->setType($typeModel->getInheritance());
                                    }
                                }
                            }
                            break;
                        default:
                            $modelAttribute->addMeta($attributeAttribute->getName(), $attributeAttribute->getValue());
                            break;
                    }
                }
            }

        }
    }
}
