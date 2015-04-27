<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\Wsdl;

class WsdlContainer extends ModelContainer
{
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\ModelContainer::objectClass()
     * @return string
     */
    protected function objectClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\Wsdl';
    }
    /**
     * @param string $name
     * @return Wsdl|null
     */
    public function getWsdlByName($name)
    {
        return $this->get($name, parent::KEY_NAME);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::get()
     * @return Wsdl|null
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::getAs()
     * @return Wsdl|null
     */
    public function getAs(array $properties)
    {
        return parent::getAs($properties);
    }
}
