<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\AttributeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagRestriction as Restriction;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\Schema;
use WsdlToPhp\PackageGenerator\Model\Struct;

class TagRestriction extends AbstractTagParser
{
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parseWsdl()
     */
    protected function parseWsdl(Wsdl $wsdl)
    {
        foreach ($this->getTags() as $tag) {
            if ($tag instanceof Restriction && $tag->isEnumeration() === false) {
                $this->parseRestriction($tag);
            }
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
        return WsdlDocument::TAG_RESTRICTION;
    }
    /**
     * @param Tag $tag
     * @param Restriction $restriction
     */
    public function parseRestriction(Restriction $restriction)
    {
        $parent = $restriction->getSuitableParent();
        if ($parent instanceof Tag) {
            $model = $this->getModel($parent);
            if ($model instanceof Struct) {
                $this->getGenerator()->getStructs()->addVirtualStruct($parent->getAttributeName());
                $this->parseRestrictionAttributes($parent, $model, $restriction);
                $this->parseRestrictionChildren($parent, $restriction);
            }
        }
    }
    /**
     * @param Tag $parent
     * @param Struct $struct
     * @param Restriction $restriction
     */
    private function parseRestrictionAttributes(Tag $parent, Struct $struct, Restriction $restriction)
    {
        if ($restriction->hasAttributes()) {
            foreach ($restriction->getAttributes() as $attribute) {
                $this->parseRestrictionAttribute($parent, $struct, $attribute);
            }
        }
    }
    /**
     * @param Tag $parent
     * @param Struct $struct
     * @param AttributeHandler $attribute
     */
    private function parseRestrictionAttribute(Tag $parent, Struct $struct, AttributeHandler $attribute)
    {
        if ($attribute->getName() === 'base' && $attribute->getValue() !== $parent->getAttributeName()) {
            $struct->setInheritance($attribute->getValue());
        } else {
            $struct->addMeta($attribute->getName(), $attribute->getValue(true));
        }
    }
    /**
     * @param Tag $parent
     * @param Restriction $restriction
     */
    private function parseRestrictionChildren(Tag $parent, Restriction $restriction)
    {
        foreach ($restriction->getElementChildren() as $child) {
            if ($parent instanceof Tag) {
                $this->parseRestrictionChild($parent, $child);
            }
        }
    }
    /**
     * @param Tag $tag
     * @param Tag $child
     */
    private function parseRestrictionChild(Tag $tag, Tag $child)
    {
        if ($child->hasAttributeValue() && ($model = $this->getModel($tag)) instanceof Struct) {
            $model->addMeta($child->getName(), $child->getAttributeValue(true));
        }
    }
}
