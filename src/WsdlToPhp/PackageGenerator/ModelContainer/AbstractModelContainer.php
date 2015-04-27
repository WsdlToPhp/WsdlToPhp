<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Generator\AbstractContainer;
use WsdlToPhp\PackageGenerator\Model\AbstractModel;

abstract class AbstractModelContainer extends AbstractContainer
{
    const
        KEY_NAME  = 'name',
        KEY_VALUE = 'value';
    /**
     * @return AbstractModel
     */
    public function get($value, $key = self::KEY_NAME)
    {
        return parent::get($value, $key);
    }
}
