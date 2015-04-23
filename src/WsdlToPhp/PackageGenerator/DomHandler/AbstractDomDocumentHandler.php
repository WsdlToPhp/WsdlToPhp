<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

abstract class AbstractDomDocumentHandler
{
    /**
     * @var \DOMDocument
     */
    protected $domDocument;
    /**
     * @var AbsractNodeHandler
     */
    protected $rootNode;
    /**
     * @param \DOMDocument $domDocument
     */
    public function __construct(\DOMDocument $domDocument)
    {
        $this->domDocument = $domDocument;
        $this->initRootNode();
    }
    /**
     * Find valid root node (not a comment, at least a DOMElement node)
     * @throws \InvalidArgumentException
     */
    protected function initRootNode()
    {
        if ($this->domDocument->hasChildNodes()) {
            $index = 0;
            foreach ($this->domDocument->childNodes as $node) {
                if ($node instanceof \DOMElement) {
                    $this->rootNode = $this->getNodeHandler($node, $this, $index);
                    $index++;
                    break;
                }
            }
        } else {
            throw new \InvalidArgumentException('Document seems to be empty');
        }
    }
    /**
     * Return the matching node handler based on current \DomNode type
     * @param \DOMNode $node
     * @param int $index
     * @return AbstractNodeHandler|AbstractElementHandler
     */
    public function getHandler(\DOMNode $node, $index = -1)
    {
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE:
                return $this->getElementHandler($node, $this, $index);
                break;
            default:
                return $this->getNodeHandler($node, $this, $index);
                break;
        }
    }
    /**
     * @param \DOMNode $node
     * @param AbstractDomDocumentHandler $domDocument
     * @param int $index
     * @return AbstractNodeHandler
     */
    abstract protected function getNodeHandler(\DOMNode $node, AbstractDomDocumentHandler $domDocument, $index = -1);
    /**
     * @param \DOMElement $element
     * @param AbstractDomDocumentHandler $domDocument
     * @param int $index
     * @return AbstractElementHandler
     */
    abstract protected function getElementHandler(\DOMElement $element, AbstractDomDocumentHandler $domDocument, $index = -1);
    /**
     * @param string $name
     * @return AbstractNodeHandler
     */
    public function getNodeByName($name)
    {
        return $this->domDocument->getElementsByTagName($name)->length > 0 ? $this->getNodeHandler($this->domDocument->getElementsByTagName($name)->item(0), $this) : null;
    }
    /**
     * @param string $name
     * @return ElementHandler
     */
    public function getElementByName($name)
    {
        $node = $this->getNodeByName($name);
        if ($node->getNode() instanceof \DOMElement) {
            return $this->getElementHandler($node->getNode(), $this);
        }
        return null;
    }
    /**
     * @param string $name
     * @return array[AbstractNodeHandler]
     */
    public function getNodesByName($name)
    {
        $nodes = array();
        if ($this->domDocument->getElementsByTagName($name)->length > 0) {
            foreach ($this->domDocument->getElementsByTagName($name) as $index=>$node) {
                $nodes[] = $this->getNodeHandler($node, $this, $index);
            }
        }
        return $nodes;
    }
    /**
     * @param string $name
     * @return array[AbstractElementHandler]
     */
    public function getElementsByName($name)
    {
        $nodes    = $this->getNodesByName($name);
        $elements = array();
        if (!empty($nodes)) {
            $index = 0;
            foreach ($nodes as $node) {
                if ($node->getNode() instanceof \DOMElement) {
                    $elements[] = $this->getElementHandler($node->getNode(), $this, $index);
                    $index++;
                }
            }
        }
        return $elements;
    }
}
