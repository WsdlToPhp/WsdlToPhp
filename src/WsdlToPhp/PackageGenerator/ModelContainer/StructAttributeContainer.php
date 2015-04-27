<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\StructAttribute;

class StructAttributeContainer extends ModelContainer
{
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\ModelContainer::objectClass()
     * @return string
     */
    protected function objectClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\StructAttribute';
    }
    /**
     * @param string $name
     * @return StructAttribute|null
     */
    public function getStructAttributeByName($name)
    {
        return $this->get($name, parent::KEY_NAME);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::get()
     * @return StructAttribute|null
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::getAs()
     * @return StructAttribute|null
     */
    public function getAs(array $properties)
    {
        return parent::getAs($properties);
    }
}
