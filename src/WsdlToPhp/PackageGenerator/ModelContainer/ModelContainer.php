<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

class ModelContainer extends AbstractModelContainer
{
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::objectClass()
     * @return string
     */
    protected function objectClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\EmptyModel';
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::get()
     * @return EmptyModel|null
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::getAs()
     * @return EmptyModel|null
     */
    public function getAs(array $properties)
    {
        return parent::getAs($properties);
    }
}
