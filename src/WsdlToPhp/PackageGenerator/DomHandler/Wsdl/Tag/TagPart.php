<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

class TagPart extends AbstractTag
{
    const
        ATTRIBUTE_ELEMENT  = 'element',
        ATTRIBUTE_TYPE     = 'type';
    /**
     * @return string
     */
    public function getAttributeElement()
    {
        return $this->hasAttribute(self::ATTRIBUTE_ELEMENT) === true ? $this->getAttribute(self::ATTRIBUTE_ELEMENT)->getValue() : '';
    }
    /**
     * @return string
     */
    public function getAttributeType()
    {
        return $this->hasAttribute(self::ATTRIBUTE_TYPE) === true ? $this->getAttribute(self::ATTRIBUTE_TYPE)->getValue() : '';
    }
}
