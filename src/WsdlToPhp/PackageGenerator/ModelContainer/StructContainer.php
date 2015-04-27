<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\Struct;

class StructContainer extends ModelContainer
{
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\ModelContainer::modelClass()
     * @return string
     */
    protected function modelClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\Struct';
    }
    /**
     * @param string $name
     * @return Struct|null
     */
    public function getStructByName($name)
    {
        return $this->get($name, parent::KEY_NAME);
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
    public function addStruct($structName, $attributeName, $attributeType)
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
     * @return Struct|null
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::getAs()
     * @return Struct|null
     */
    public function getAs(array $properties)
    {
        return parent::getAs($properties);
    }
}
