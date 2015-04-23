<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

abstract class AbstractDomDocumentHandler
{
    /**
     * @var \DOMDocument
     */
    protected $domDocument;
    /**
     * @param \DOMDocument $domDocument
     */
    public function __construct(\DOMDocument $domDocument)
    {
        $this->domDocument = $domDocument;
    }
    /**
     * @param string $name
     * @throws \InvalidArgumentException
     * @return null|AbstractNodeHandler
     */
    public function getNodeByName($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name must be defined!');
        }
        return $this->domDocument->getElementsByTagName($name)->length > 0 ? $this->getDomNodeHandler($this->domDocument->getElementsByTagName($name)->item(0)) : null;
    }
    /**
     * @param \DOMNode $node
     * @return AbstractNodeHandler
     */
    abstract protected function getDomNodeHandler(\DOMNode $node);
}
