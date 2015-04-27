<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

class MethodContainer extends ModelContainer
{
    const KEY_PARAMETER_TYPE = 'parameterType';
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\ModelContainer::objectClass()
     * @return string
     */
    protected function objectClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\Method';
    }
    /**
     * @param sring $name
     * @return Method|null
     */
    public function getMethodByName($name)
    {
        return $this->get($name, parent::KEY_NAME);
    }
    /**
     * @param string $name
     * @param mixed $parameterType
     * @return boolean
     */
    public function hasMethod($name, $parameterType)
    {
        return null !== $this->getAs(array(
            parent::KEY_NAME         => $name,
            self::KEY_PARAMETER_TYPE => $parameterType,
        ));
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::get()
     * @return Method|null
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::getAs()
     * @return Method|null
     */
    public function getAs(array $properties)
    {
        return parent::getAs($properties);
    }
}
