<?php

namespace WsdlToPhp\PackageGenerator\Tests\DomHandler;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler;

class DomDocumentHandler extends AbstractDomDocumentHandler
{
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler::getDomNodeHandler()
     * @return NodeHandler
     */
    protected function getDomNodeHandler(\DOMNode $node)
    {
        return new NodeHandler($node);
    }
}
