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
                    $this->rootNode = $this->getNodeHandler($node);
                    break;
                }
            }
        } else {
            throw new \InvalidArgumentException('Document seems to be empty');
        }
    }
    /**
     * @param \DOMNode $node
     * @return AbstractNodeHandler
     */
    abstract protected function getNodeHandler(\DOMNode $node);
    /**
     * @param string $name
     * @return AbstractNodeHandler
     */
    public function getNodeByName($name)
    {
        return $this->domDocument->getElementsByTagName($name)->length > 0 ? $this->getNodeHandler($this->domDocument->getElementsByTagName($name)->item(0)) : null;
    }
    /**
     * @param string $name
     * @return array[AbstractNodeHandler]
     */
    public function getNodesByName($name)
    {
        $nodes = array();
        if ($this->domDocument->getElementsByTagName($name)->length > 0) {
            foreach ($this->domDocument->getElementsByTagName($name) as $node) {
                $nodes[] = $this->getNodeHandler($node);
            }
        }
        return $nodes;
    }
}
