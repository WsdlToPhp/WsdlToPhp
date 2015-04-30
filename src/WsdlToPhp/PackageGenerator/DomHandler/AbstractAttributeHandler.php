<?php

namespace WsdlToPhp\PackageGenerator\DomHandler;

use WsdlToPhp\PackageGenerator\Generator\Utils;

class AbstractAttributeHandler extends AbstractNodeHandler
{
    const
        ATTRIBUTE_NAME  = 'name',
        ATTRIBUTE_VALUE = 'value',
        ATTRIBUTE_TYPE  = 'type';
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractNodeHandler::getNode()
     * @return \DOMAttr
     */
    public function getNode()
    {
        return parent::getNode();
    }
    /**
     * @return \DOMAttr
     */
    public function getAttribute()
    {
        return $this->getNode();
    }
    /**
     * Tries to get attribute type on the same node
     * in order to return the value of the attribute in its type
     */
    public function getType()
    {
        $type = null;
        if ($this->getParent() instanceof ElementHandler && $this->getParent()->hasAttribute('type')) {
            $type = $this->getParent()->getAttribute('type');
        }
        return $type;
    }
    /**
     * @param bool $withNamespace
     * @return string
     */
    public function getValue($withNamespace = false, $withinItsType = true)
    {
        $value = $this->getAttribute()->value;
        if ($withNamespace === false && !empty($value)) {
            $value = implode('', array_slice(explode(':', $value), -1, 1));
        }
        if ($value !== null && $withinItsType === true) {
            $value = Utils::getValueWithinItsType($value, $this->getType());
        }
        return $value;
    }
    /**
     * @return null|string
     */
    public function getValueNamespace()
    {
        $value     = $this->getAttribute()->value;
        $namespace = null;
        if (strpos($value, ':') !== false) {
            $namespace = implode('', array_slice(explode(':', $value), 0, -1));
        }
        return $namespace;
    }
}
