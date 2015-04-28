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
            foreach ($this->domDocument->childNodes as $node) {
                if ($node instanceof \DOMElement) {
                    $this->rootNode = $this->getNodeHandler($node, $this);
                    break;
                }
            }
        } else {
            throw new \InvalidArgumentException('Document seems to be invalid');
        }
    }
    /**
     * Return the matching node handler based on current \DomNode type
     * @param \DOMNode $node
     * @param int $index
     * @return NodeHandler|ElementHandler
     */
    public function getHandler(\DOMNode $node, $index = -1)
    {
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE:
                return $this->getElementHandler($node, $this, $index);
                break;
            case XML_ATTRIBUTE_NODE:
                return $this->getAttributeHandler($node, $this, $index);
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
     * @return NodeHandler
     */
    abstract protected function getNodeHandler(\DOMNode $node, AbstractDomDocumentHandler $domDocument, $index = -1);
    /**
     * @param \DOMElement $element
     * @param AbstractDomDocumentHandler $domDocument
     * @param int $index
     * @return ElementHandler
     */
    abstract protected function getElementHandler(\DOMElement $element, AbstractDomDocumentHandler $domDocument, $index = -1);
    /**
     * @param \DOMAttr $element
     * @param AbstractDomDocumentHandler $domDocument
     * @param int $index
     * @return AttributeHandler
     */
    abstract protected function getAttributeHandler(\DOMAttr $attribute, AbstractDomDocumentHandler $domDocument, $index = -1);
    /**
     * @param string $name
     * @return NodeHandler
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
     * @return array[NodeHandler]
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
     * @return array[ElementHandler]
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
    /**
     * @param string $name
     * @param array $attributes
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\ElementHandler]
     */
    public function getElementsByNameAndAttributes($name, array $attributes)
    {
        $matchingElements = $elements = $this->getElementsByName($name);
        if (!empty($attributes) && !empty($elements)) {
            $matchingElements = array();
            foreach ($elements as $element) {
                if ($element->hasAttributes()) {
                    $elementMatches = true;
                    foreach ($attributes as $attributeName=>$attributeValue) {
                        $elementMatches &= $element->hasAttribute($attributeName) ? $element->getAttribute($attributeName)->getValue() === $attributeValue : false;
                    }
                    if ((bool)$elementMatches === true) {
                        $matchingElements[] = $element;
                    }
                }
            }
        }
        return $matchingElements;
    }
}
