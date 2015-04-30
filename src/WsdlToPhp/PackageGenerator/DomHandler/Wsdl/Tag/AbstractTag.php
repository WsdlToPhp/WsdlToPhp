<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler as Attribute;
use WsdlToPhp\PackageGenerator\DomHandler\ElementHandler;

abstract class AbstractTag extends ElementHandler
{
    /**
     * @var int
     */
    const MAX_DEEP = 5;
    /**
     * This method aims to get the parent element that matches a valid Wsdl element (aka struct)
     * @param string $checkName whether to validate the attribute named "name" or not
     * @param array $additionalTags
     * @param int $maxDeep
     * @return null|\WsdlToPhp\PackageGenerator\DomHandler\AbstractNodeHandler|\WsdlToPhp\PackageGenerator\DomHandler\AbstractElementHandler|\WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler|\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag
     */
    public function getSuitableParent($checkName = true, array $additionalTags = array(), $maxDeep = self::MAX_DEEP)
    {
        $parentNode = null;
        if ($this->getParent() !== null) {
            $parentTags = $this->getSuitableParentTags($additionalTags);
            $parentNode = $this->getParent()->getNode();
            while ($maxDeep-- > 0 && ($parentNode instanceof \DOMElement) && !empty($parentNode->nodeName) && (!preg_match('/' . implode('|', $parentTags) . '/i', $parentNode->nodeName) || ($checkName && preg_match('/' . implode('|', $parentTags) . '/i', $parentNode->nodeName) && (!$parentNode->hasAttribute('name') || $parentNode->getAttribute('name') === '')))) {
                $parentNode = $parentNode->parentNode;
            }
            if ($parentNode instanceof \DOMElement) {
                $parentNode = $this->getDomDocumentHandler()->getHandler($parentNode);
            } else {
                $parentNode = null;
            }
        }
        return $parentNode;
    }
    /**
     * Suitable tags as parent
     * @return array[string]
     */
    protected function getSuitableParentTags(array $additionalTags = array())
    {
        return array_merge(array(
            WsdlDocument::TAG_ELEMENT,
            WsdlDocument::TAG_ATTRIBUTE,
            WsdlDocument::TAG_SIMPLE_TYPE,
            WsdlDocument::TAG_COMPLEX_TYPE,
        ), $additionalTags);
    }
    /**
     * @return bool
     */
    public function hasAttributeName()
    {
        return $this->hasAttribute(Attribute::ATTRIBUTE_NAME);
    }
    /**
     * @return null|string
     */
    public function getAttributeName()
    {
        return $this->getAttribute(Attribute::ATTRIBUTE_NAME) ? $this->getAttribute(Attribute::ATTRIBUTE_NAME)->getValue() : '';
    }
    /**
     * @return boolean
     */
    public function hasAttributeValue()
    {
        return $this->hasAttribute(Attribute::ATTRIBUTE_VALUE);
    }
    /**
     * @return null|string
     */
    public function getAttributeValue()
    {
        return $this->getAttribute(Attribute::ATTRIBUTE_VALUE) ? $this->getAttribute(Attribute::ATTRIBUTE_VALUE)->getValue() : '';
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractElementHandler::getChildrenByName()
     * @return array[AbstractTag]
     */
    public function getChildrenByName($name)
    {
        $this->getDomDocumentHandler()->setCurrentTag($name);
        return parent::getChildrenByName($name);
    }
}
