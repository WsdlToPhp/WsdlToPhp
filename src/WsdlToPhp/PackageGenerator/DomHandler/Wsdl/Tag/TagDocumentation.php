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
     * Finds parent node of this documentation node without taking care of the name attribute for enumeration.
     * This case is managed first because enumerations are contained by elements and
     * the method could climb to its parent without stopping on the enumeration tag.
     * Indeed, depending on the node, it may contain or not the attribute named "name" so we have to split each case.
     * @see \WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag::getSuitableParent()
     */
    public function getSuitableParent($checkName = true, array $additionalTags = array(), $maxDeep = self::MAX_DEEP, $strict = false)
    {
        if ($strict === false) {
            $enumerationTag = $this->getStrictParent(WsdlDocument::TAG_ENUMERATION);
            if ($enumerationTag instanceof TagEnumeration) {
                return $enumerationTag;
            }
        }
        // Reset current tag as using getStrictParent method set currentTag to enumeration
        // as soon as currentTag has been set, if a valid DOMElement is found
        // then without taking care of the actual DOMElement tag name,
        // a TagEnumeration is always returned.
        // Moreover, we reset current tag only if we're not in the case of the call
        // for the current $this->getStrictParent(WsdlDocument::TAG_ENUMERATION); call.
        // @todo If it's possible, find a cleaner way to solve this 'issue'
        if ($strict === false) {
            $this->getDomDocumentHandler()->setCurrentTag('');
        }
        return parent::getSuitableParent($checkName, $additionalTags, $maxDeep, $strict);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag::getSuitableParentTags()
     */
    public function getSuitableParentTags(array $additionalTags = array())
    {
        return parent::getSuitableParentTags(array_merge($additionalTags, array(
            WsdlDocument::TAG_OPERATION,
        )));
    }
}
