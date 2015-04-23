<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

abstract class AbstractNodeHandler
{
    /**
     * @var \DOMNode
     */
    protected $node;
    /**
     * @param \DOMNode $node
     */
    public function __construct(\DOMNode $node)
    {
        $this->node = $node;
    }
}
