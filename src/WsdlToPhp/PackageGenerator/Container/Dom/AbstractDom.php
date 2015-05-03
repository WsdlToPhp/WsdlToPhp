<?php

namespace WsdlToPhp\PackageGenerator\Container\Model;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractNodeHandler;
use WsdlToPhp\PackageGenerator\Container\AbstractNodeListContainer;

abstract class AbstractDom extends AbstractNodeListContainer
{
    const
        KEY_NAME  = 'name',
        KEY_VALUE = 'value';
    /**
     * @return AbstractNodeHandler
     */
    public function get($value, $key = self::KEY_NAME)
    {
        return parent::get($value, $key);
    }
}
