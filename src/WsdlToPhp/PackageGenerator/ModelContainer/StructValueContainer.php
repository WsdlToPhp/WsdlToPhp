<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\StructValue;

class StructValueContainer extends ModelContainer
{
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\ModelContainer::modelClass()
     * @return string
     */
    protected function modelClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\StructValue';
    }
    /**
     * @param string $name
     * @return StructValue|null
     */
    public function getStructValueByName($name)
    {
        return $this->get($name, parent::KEY_NAME);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::get()
     * @return StructValue|null
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::getAs()
     * @return StructValue|null
     */
    public function getAs(array $properties)
    {
        return parent::getAs($properties);
    }
}
