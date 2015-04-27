<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

class TagEnumeration extends AbstractTag
{
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->getAttributeValue() ? $this->getAttributeValue()->getValue() : '';
    }
}
