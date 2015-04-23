<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler;

class DomDocumentHandler extends AbstractDomDocumentHandler
{
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler::getNodeHandler()
     * @return NodeHandler
     */
    protected function getNodeHandler(\DOMNode $node)
    {
        return new NodeHandler($node);
    }
    /**
     * @return NodeHandler
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }
}
