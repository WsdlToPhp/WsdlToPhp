<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

class TagDocumentation extends AbstractTag
{
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getNodeValue();
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag::getSuitableParentTags()
     */
    public function getSuitableParentTags(array $additionalTags = array())
    {
        return array_merge(parent::getSuitableParentTags($additionalTags), array(
            WsdlDocument::TAG_OPERATION,
            WsdlDocument::TAG_ENUMERATION,
        ));
    }
}
