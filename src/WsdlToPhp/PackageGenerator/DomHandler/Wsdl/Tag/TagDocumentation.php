<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

class TagDocumentation extends AbstractTag
{
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getNodeValue();
    }
}
