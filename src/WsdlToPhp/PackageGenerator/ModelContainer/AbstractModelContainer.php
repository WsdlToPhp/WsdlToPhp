<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\AbstractModel;

abstract class AbstractModelContainer implements \ArrayAccess, \Iterator, \Countable
{
    const
        KEY_NAME  = 'name',
        KEY_VALUE = 'value';
    /**
     * @var array[AbstractModel]
     */
    protected $models;
    /**
     * @var int
     */
    protected $offset;
    /**
     * Very simple cache, holds searches in order to improve preformance for big web services
     * @var array
     */
    private static $cache = array();
    /**
     * @return AbstractModelContainer
     */
    public function __construct()
    {
        $this->offset = 0;
        $this->models = array();
        $this->cache  = array();
    }
    /**
     * @param int $offset
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->models);
    }
    /**
     * @param int $offset
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->models[$offset] : null;
    }
    /**
     * @param int $offset
     * @param mixed $value
     * @return AbstractModelHolder
     */
    public function offsetSet($offset, $value)
    {
        $this->models[$offset] = $value;
        return $this;
    }
    /**
     * @param int $offset
     * @return AbstractModelHolder
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->models[$offset]);
        }
        return $this;
    }
    /**
     * @return mixed
     */
    public function current()
    {
        return $this->offsetGet($this->offset);
    }
    /**
     * @return mixed
     */
    public function next()
    {
        $this->offset++;
    }
    /**
     * @return int
     */
    public function key()
    {
        $this->offset;
    }
    /**
     * @return bool
     */
    public function valid()
    {
        return $this->offsetExists($this->offset);
    }
    /**
     * @return AbstractModelContainer
     */
    public function rewind()
    {
        $this->offset = 0;
        return $this;
    }
    /**
     * @return int
     */
    public function count()
    {
        return count($this->models);
    }
    /**
     * Must return the model class name that this container is made to contain
     */
    abstract protected function modelClass();
    /**
     * @param unknown $model
     * @throws \InvalidArgumentException
     * @return AbstractModelContainer
     */
    public function add($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(sprintf('You must only pass object to this container, "%s" passed as parameter!', gettype($model)));
        }
        if (get_class($model) !== $this->modelClass()) {
            throw new \InvalidArgumentException(sprintf('Model of type "%s" does not match the model contained by this class: "%s"', get_class($model), $this->modelClass()));
        }
        $this->models[] = $model;
        return $this;
    }
    /**
     * @param string $key
     * @param string $value
     * @return AbstractModel
     */
    public function get($value, $key = self::KEY_NAME)
    {
        if ($this->count() > 0) {
            $cacheValues = array(
                'class'       => get_called_class(),
                'model_class' => $this->modelClass(),
                'value'       => $value,
                'key'         => $key,
            );
            $cachedModel = self::getCache($cacheValues);
            if ($cachedModel === null) {
                foreach ($this->models as $model) {
                    $get = sprintf('get%s', ucfirst($key));
                    if (!method_exists($model, $get)) {
                        throw new \InvalidArgumentException(sprintf('Property "%s" does not exist or its getter does not exist', $key));
                    }
                    $propertyValue = call_user_func(array(
                        $model,
                        $get,
                    ));
                    if ($value === $propertyValue) {
                        self::setCache($cacheValues, $model);
                        return $model;
                    }
                }
            }
            return $cachedModel;
        }
        return null;
    }
    /**
     * @param array $properties
     * @throws \InvalidArgumentException
     * @return AbstractModel|null
     */
    public function getAs(array $properties)
    {
        if ($this->count() > 0) {
            $cacheValues = array(
                'class'       => get_called_class(),
                'model_class' => $this->modelClass(),
                'properties'  => $properties,
            );
            $cachedModel = self::getCache($properties);
            if ($cachedModel === null) {
                foreach ($this->models as $model) {
                    $same = true;
                    foreach ($properties as $name=>$value) {
                        $get = sprintf('get%s', ucfirst($name));
                        if (!method_exists($model, $get)) {
                            throw new \InvalidArgumentException(sprintf('Property "%s" does not exist or its getter does not exist', $name));
                        }
                        $propertyValue = call_user_func(array(
                            $model,
                            $get,
                        ));
                        $same &= $propertyValue === $value;
                    }
                    if ((bool)$same === true) {
                        self::setCache($cacheValues, $model);
                        return $model;
                    }
                }
            }
            return $cachedModel;
        }
        return null;
    }
    /**
     * @param array $values
     * @return mixed
     */
    private static function getCache(array $values)
    {
        $key = self::cacheKey($values);
        return array_key_exists($key, self::$cache) ? self::$cache[$key] : null;
    }
    /**
     * @param array $values
     * @return AbstractModelContainer
     */
    private static function purgeCache(array $values)
    {
        if (self::getCache($values)) {
            unset(self::$cache[self::cacheKey($values)]);
        }
    }
    /**
     * @param array $values
     * @param mixed $value
     * @return AbstractModelContainer
     */
    private static function setCache(array $values, $value)
    {
        self::$cache[self::cacheKey($values)] = $value;
    }
    /**
     * @param array $values
     * @return string
     */
    private static function cacheKey(array $values)
    {
        return sprintf('_%s', var_export($values, true));
    }
}
