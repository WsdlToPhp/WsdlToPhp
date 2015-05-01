<?php
namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagRestriction as Restriction;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagEnumeration as Enumeration;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAnnotation as Annotation;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAppinfo as Appinfo;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagDocumentation as Documentation;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagExtension as Extension;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAttribute as Attribute;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagElement as Element;
use WsdlToPhp\PackageGenerator\Model\Struct;
use WsdlToPhp\PackageGenerator\Model\Method;
use WsdlToPhp\PackageGenerator\Generator\Generator;

abstract class AbstractTagParser extends AbstractParser
{
    /**
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }
    /**
     * Return the model on which the method will be called
     * @param Tag $tag
     * @return Struct|Method
     */
    protected function getModel(Tag $tag)
    {
        $model = null;
        switch ($tag->getName()) {
            case WsdlDocument::TAG_OPERATION:
                $model = $this->generator->getServiceMethod($tag->getAttributeName());
                break;
            default:
                $model = $this->generator->getStruct($tag->getAttributeName());
                break;
        }
        return $model;
    }
    /**
     * @param Tag $tag
     */
    protected function parseRestrictions(Tag $tag)
    {
        $restrictions = $tag->getChildrenByName(WsdlDocument::TAG_RESTRICTION);
        foreach ($restrictions as $restriction) {
            if ($restriction->isEnumeration() === false) {
                $this->parseRestriction($restriction);
            } else {
                foreach ($restriction->getEnumerations() as $enumeration) {
                    $this->parseEnumeration($enumeration);
                }
            }
        }
    }
    /**
     * @param Tag $tag
     * @param Restriction $restriction
     */
    protected function parseRestriction(Restriction $restriction)
    {
        $parent = $restriction->getSuitableParent();
        if ($parent !== null) {
            $this->getGenerator()->getStructs()->addVirtualStruct($parent->getAttributeName());

            if ($restriction->hasAttributes()) {
                foreach ($restriction->getAttributes() as $attribute) {
                    if ($attribute->getName() === 'base' && $attribute->getValue() !== $parent->getAttributeName()) {
                        if ($this->getModel($parent) !== null) {
                            $this->getModel($parent)->setInheritance($attribute->getValue());
                        }
                    } else {
                        $this->addMetaFromAttribute($parent, $attribute);
                    }
                }
            }

            foreach ($restriction->getElementChildren() as $child) {
                $this->parseRestrictionChild($parent, $child);
            }
        }
    }
    /**
     * @param Tag $tag
     * @param Attribute $attribute
     */
    private function addMetaFromAttribute(Tag $tag, Attribute $attribute)
    {
        if ($this->getModel($tag) !== null) {
            $this->getModel($tag)->addMeta($attribute->getName(), $attribute->getValue());
        }
    }
    /**
     * @param Tag $tag
     * @param Tag $child
     */
    private function parseRestrictionChild(Tag $tag, Tag $child)
    {
        if ($child->hasAttributeValue() && $this->getModel($tag) !== null) {
            $this->getModel($tag)->addMeta($child->getName(), $child->getAttributeValue());
        }
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
    private function addStructValue(Tag $tag, Enumeration $enumeration)
    {
        $struct = $this->getModel($tag);
        if ($struct instanceof  Struct) {
            $struct->addValue($enumeration->getValue());
        }
    }
    /**
     * @param Tag $tag
     */
    protected function parseAnnotations(Tag $tag)
    {
        $annotations = $tag->getChildrenByName(WsdlDocument::TAG_ANNOTATION);
        foreach ($annotations as $annotation) {
            $this->parseAnnotation($annotation);
        }
    }
    /**
     * @param Annotation $annotation
     */
    private function parseAnnotation(Annotation $annotation)
    {
        $appinfos = $annotation->getChildrenByName(WsdlDocument::TAG_APPINFO);
        foreach ($appinfos as $appinfo) {
            $this->parseAppinfo($appinfo);
        }
        $documentations = $annotation->getChildrenByName(WsdlDocument::TAG_DOCUMENTATION);
        foreach ($documentations as $documentation) {
            $this->parseDocumentation($documentation);
        }
    }
    /**
     * @param Appinfo $appinfo
     */
    private function parseAppinfo(Appinfo $appinfo)
    {
        $content = $appinfo->getContent();
        $parent  = $appinfo->getSuitableParent();
        if (!empty($content) && $parent !== null && $this->getModel($parent) !== null) {
            $this->getModel($parent)->addMeta('appinfo', $content);
        }
    }
    /**
     * @param Documentation $documentation
     */
    protected function parseDocumentation(Documentation $documentation)
    {
        $content      = $documentation->getContent();
        $parent       = $documentation->getSuitableParent();
        $parentParent = $parent !== null ? $parent->getSuitableParent() : null;

        if (!empty($content) && $parent !== null) {
            /**
             * Is it an element ? part of a struct
             * Finds parent node of this documentation node
             */
            if ($parent->hasAttribute('type') && $parentParent !== null) {
                if ($this->getModel($parentParent) instanceof Struct && $this->getModel($parentParent)->getAttribute($parent->getAttributeName())) {
                    $this->getModel($parentParent)->getAttribute($parent->getAttributeName())->setDocumentation($content);
                }
            } elseif($parent->getName() === WsdlDocument::TAG_ENUMERATION) {
                if ($parentParent !== null && $this->getModel($parentParent) !== null && $this->getModel($parentParent)->getValue($parent->getAttributeName()) !== null) {
                    $this->getModel($parentParent)->getValue($parent->getAttributeName())->setDocumentation($content);
                }
            } elseif ($this->getModel($parent) !== null) {
                $this->getModel($parent)->setDocumentation($content);
            }
        }
    }
    /**
     * @param Extension $extension
     */
    protected function parseExtension(Extension $extension)
    {
        $base   = $extension->getAttribute('base')->getValue();
        $parent = $extension->getSuitableParent();
        if (!empty($base) && $parent !== null && $this->getModel($parent) !== null && $parent->getAttributeName() !== $base) {
            $this->getModel($parent)->setInheritance($base);
        }
    }
    /**
     * @param Attribute $attribute
     */
    protected function parseAttribute(Attribute $attribute)
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
    /**
     * @param Element $element
     */
    protected function parseElement(Element $element)
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