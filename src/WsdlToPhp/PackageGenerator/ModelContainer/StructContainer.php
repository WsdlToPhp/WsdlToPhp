<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\Struct;

class StructContainer extends ModelContainer
{
    protected function modelClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\Struct';
    }
    /**
     * Adds a virtual struct
     * @param string $structName the original struct name
     * @return StructContainer
     */
    public function addVirtualStruct($structName)
    {
        if ($this->get($structName) === null) {
            $this->add(new Struct($structName, false));
        }
        return $this;
    }
    /**
     * Adds type to structs
     * @param string $structName the original struct name
     * @param string $attributeName the attribute name
     * @param string $attributeType the attribute type
     * @return StructContainer
     */
    private function addStruct($structName, $attributeName, $attributeType)
    {
        if ($this->get($structName) === null) {
            $this->add(new Struct($structName));
        }
        if (!empty($attributeName) && !empty($attributeType) && $this->get($structName) !== null) {
            $this->get($structName)->addAttribute($attributeName, $attributeType);
        }
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::get()
     * @return Struct
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
}
