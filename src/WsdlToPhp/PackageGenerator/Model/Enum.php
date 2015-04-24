<?php

namespace WsdlToPhp\PackageGenerator\Model;

class Enum extends Struct
{
    public function __construct($name, $isStruct = true)
    {
        parent::__construct($name, $isStruct);
        $this->setIsRestriction(true);
    }
}
