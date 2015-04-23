<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

class AbstractElementHandler extends AbstractNodeHandler
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
}
