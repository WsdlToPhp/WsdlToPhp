<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler as Attribute;

class TagHeader extends AbstractTag
{
    const
        ATTRIBUTE_MESSAGE  = 'message',
        ATTRIBUTE_PART     = 'part',
        ATTRIBUTE_REQUIRED = 'required';
    /**
     * @return TagOperation|null
     */
    public function getParentOperation()
    {
        return $this->getStrictParent(WsdlDocument::TAG_OPERATION);
    }
    /**
     * @return TagInput|null
     */
    public function getParentInput()
    {
        return $this->getStrictParent(WsdlDocument::TAG_INPUT);
    }
    /**
     * @return string
     */
    public function getAttributePart()
    {
        return $this->getAttribute(self::ATTRIBUTE_PART) !== null ? $this->getAttribute(self::ATTRIBUTE_PART)->getValue() : '';
    }
    /**
     * @return string
     */
    public function getAttributeMessage()
    {
        return $this->getAttribute(self::ATTRIBUTE_MESSAGE) !== null ? $this->getAttribute(self::ATTRIBUTE_MESSAGE)->getValue() : '';
    }
    /**
     * @return string
     */
    public function getAttributeRequired()
    {
        return $this->getAttribute(self::ATTRIBUTE_REQUIRED) !== null ? $this->getAttribute(self::ATTRIBUTE_REQUIRED)->getValue() : '';
    }
    /**
     * @return string
     */
    public function getAttributeNamespace()
    {
        return $this->getAttribute(Attribute::ATTRIBUTE_NAMESPACE) !== null ? $this->getAttribute(Attribute::ATTRIBUTE_NAMESPACE)->getValue() : '';
    }
}
