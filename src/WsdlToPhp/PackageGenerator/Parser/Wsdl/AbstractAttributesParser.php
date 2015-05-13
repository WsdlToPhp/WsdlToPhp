<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\Schema;

abstract class AbstractAttributesParser extends AbstractTagParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag]
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
            $this->parseTag($tag);
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
     * @param Attribute $tag
     */
    public function parseTag(AbstractTag $tag)
    {
        $parent = $tag->getSuitableParent();
        if ($parent !== null && $tag->hasAttributeName() && ($model = $this->getModel($parent)) !== null && ($modelAttribute = $model->getAttribute($tag->getAttributeName())) !== null) {
            foreach ($tag->getAttributes() as $tagAttribute) {
                switch ($tagAttribute->getName()) {
                    case AbstractAttributeHandler::ATTRIBUTE_NAME:
                        /**
                         * Avoid this attribute to be added as meta
                         */
                        break;
                    case AbstractAttributeHandler::ATTRIBUTE_TYPE:
                        $type = $tagAttribute->getValue();
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
                    case AbstractAttributeHandler::ATTRIBUTE_ABSTRACT:
                        $model->setIsAbstract($tagAttribute->getValue(false, true, 'bool'));
                        break;
                    default:
                        $modelAttribute->addMeta($tagAttribute->getName(), $tagAttribute->getValue());
                        break;
                }
            }
        }
    }
}