<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

class TagPart extends AbstractTag
{
    const
        ATTRIBUTE_ELEMENT = 'element',
        ATTRIBUTE_TYPE    = 'type';
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
    /**
     * @return string
     */
    public function getFinalType()
    {
        $type = $this->getAttributeType();
        if (empty($type)) {
            $elementName = $this->getAttributeElement();
            if (!empty($elementName)) {
                $element = $this->getDomDocumentHandler()->getElementByNameAndAttributes(WsdlDocument::TAG_ELEMENT, array(
                    'name' => $elementName,
                ), true);
                if ($element !== null && $element->hasAttribute(self::ATTRIBUTE_TYPE)) {
                    $type = $element->getAttribute(self::ATTRIBUTE_TYPE)->getValue();
                }
            }
        }
        return $type;
    }
}
