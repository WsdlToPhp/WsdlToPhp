<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

abstract class AbstractElementHandler extends AbstractNodeHandler
{
    /**
     * @param \DOMElement $element
     * @param AbstractDomDocumentHandler $domDocument
     * @param int $index
     * @return AbstractElementHandler
     */
    public function __construct(\DOMElement $element, AbstractDomDocumentHandler $domDocument, $index = -1)
    {
        return parent::__construct($element, $domDocument, $index);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractNodeHandler::getNode()
     * @return \DOMElement
     */
    public function getNode()
    {
        return parent::getNode();
    }
    /**
     * Alias to getNode()
     * @return \DOMElement
     */
    public function getElement()
    {
        return $this->getNode();
    }
    /**
     * @param string $name
     * @return boolean
     */
    public function hasAttribute($name)
    {
        return $this->getElement()->hasAttribute($name);
    }
    /**
     * @param string $name
     * @return null|AbstractAttributeHandler
     */
    public function getAttribute($name)
    {
        return $this->hasAttribute($name) ? $this->getDomDocumentHandler()->getHandler($this->getNode()->getAttributeNode($name)) : null;
    }
    /**
     * @param string $name
     * @return array[AbstractNodeHandler]|array[AbstractElementHandler]
     */
    public function getChildrenByName($name)
    {
        $children = array();
        if ($this->hasChildren()) {
            foreach ($this->getElement()->getElementsByTagName($name) as $index=>$node) {
                $children[] = $this->getDomDocumentHandler()->getHandler($node, $index);
            }
        }
        return $children;
    }
}
