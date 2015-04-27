<?php

namespace WsdlToPhp\PackageGenerator\Model;

class Wsdl extends AbstractModel
{
    public function getClassBody(&$class)
    {
    }
    public function __toString()
    {
        return $this->getName();
    }
}
