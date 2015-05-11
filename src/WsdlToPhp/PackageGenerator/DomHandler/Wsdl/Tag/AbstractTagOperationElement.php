<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

abstract class AbstractTagOperationElement extends AbstractTag
{
    /**
     * @return TagOperation|null
     */
    public function getParentOperation()
    {
        return $this->getStrictParent(WsdlDocument::TAG_OPERATION);
    }
}
