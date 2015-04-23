<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

class AbstractAttributeHandler
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @param string $name
     * @param string $value
     * @return AbstractAttributeHandler
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }
}
