<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

class TagUnion extends AbstractTag
{
    const
        ATTRIBUTE_MEMBER_TYPES = 'memberTypes';
    /**
     * @return array[string]
     */
    public function getAttributeMemberTypes()
    {
        return $this->parseMemberTypes();
    }
    /**
     * @return array[string]
     */
    protected function parseMemberTypes()
    {
        $memberTypes = array();
        $value = $this->hasAttribute(self::ATTRIBUTE_MEMBER_TYPES) === true ? $this->getAttribute(self::ATTRIBUTE_MEMBER_TYPES)->getValue(true) : '';
        if (!empty($value)) {
            $values = explode(' ', $value);
            foreach ($values as $val) {
                $memberTypes[] = implode('', array_slice(explode(':', $val), -1, 1));
            }
        }
        return $memberTypes;
    }
}
