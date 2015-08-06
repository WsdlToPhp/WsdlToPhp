<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\DomHandler\AttributeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

class TagPart extends AbstractTag
{
    const
        ATTRIBUTE_ELEMENT = 'element',
        ATTRIBUTE_TYPE    = 'type';
    /**
     * @param bool $returnValue
     * @return AttributeHandler|mixed
     */
    public function getAttributeElement($returnValue = true)
    {
        return $this->getAttributeMixedValue(self::ATTRIBUTE_ELEMENT, $returnValue);
    }
    /**
     * @param bool $returnValue
     * @return AttributeHandler|mixed
     */
    public function getAttributeType($returnValue = true)
    {
        return $this->getAttributeMixedValue(self::ATTRIBUTE_TYPE, $returnValue);
    }
    /**
     * @param string $attributeName
     * @param bool $returnValue
     * @return AttributeHandler|mixed
     */
    private function getAttributeMixedValue($attributeName, $returnValue = true)
    {
        $value = $this->getAttribute($attributeName);
        if ($returnValue === true && $value instanceof AttributeHandler) {
            $value = $value->getValue();
        }
        return $value;
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
                if ($element instanceof TagElement && $element->hasAttribute(self::ATTRIBUTE_TYPE)) {
                    $type = $element->getAttribute(self::ATTRIBUTE_TYPE)->getValue();
                } else {
                    $type = $elementName;
                }
            }
        }
        return $type;
    }
    /**
     * @return string
     */
    public function getFinalName()
    {
        $name = $this->getAttributeType();
        if (empty($name)) {
            $name = $this->getAttributeElement();
        }
        return $name;
    }
    /**
     * @return null|string
     */
    public function getFinalNamespace()
    {
        $attribute = $this->getAttributeType(false);
        if (empty($attribute)) {
            $attribute = $this->getAttributeElement(false);
            if (empty($attribute)) {
                return null;
            }
        }
        return $attribute->getValueNamespace();
    }
}
