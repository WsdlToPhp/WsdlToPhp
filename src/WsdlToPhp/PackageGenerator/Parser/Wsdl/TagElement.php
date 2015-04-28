<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagElement as Element;
use WsdlToPhp\PackageGenerator\Model\Wsdl;

class TagElement extends AbstractTagParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagElement]
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
            $this->parseElement($tag);
        }
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_ELEMENT;
    }
    /**
     * @param Element $element
     */
    public function parseElement(Element $element)
    {
        $parent = $element->getSuitableParent();
        if ($parent !== null && $element->hasAttributeName() && ($model = $this->getModel($parent)) !== null && ($modelAttribute = $model->getAttribute($element->getAttributeName())) !== null) {
            if ($modelAttribute !== null) {
                foreach ($element->getAttributes() as $elementAttribute) {
                    switch ($elementAttribute->getName()) {
                        case AbstractAttributeHandler::ATTRIBUTE_NAME:
                            /**
                             * Avoid this attribute to be added as meta
                             */
                            break;
                        case AbstractAttributeHandler::ATTRIBUTE_TYPE:
                            $type = $elementAttribute->getValue();
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
                            $modelAttribute->addMeta($elementAttribute->getName(), $elementAttribute->getValue());
                            break;
                    }
                }
            }

        }
    }
}
