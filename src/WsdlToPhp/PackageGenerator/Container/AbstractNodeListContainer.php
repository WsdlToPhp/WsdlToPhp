<?php

namespace WsdlToPhp\PackageGenerator\Container;

use WsdlToPhp\PackageGenerator\DomHandler\AbstractNodeHandler;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler;

abstract class AbstractNodeListContainer implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * @var AbstractDomDocumentHandler
     */
    protected $domDocument;
    /**
     * @var \DOMNodeList
     */
    protected $nodeList;
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
     * @param \DOMNodeList $nodeList
     * @param AbstractDomDocumentHandler $domDocument
     * @return AbstractNodeListContainer
     */
    public function __construct(\DOMNodeList $nodeList, AbstractDomDocumentHandler $domDocument)
    {
        $this->offset      = 0;
        $this->nodeList    = $nodeList;
        $this->domDocument = $domDocument;
    }
    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->nodeList->item($offset) !== null;
    }
    /**
     * @param int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->nodeList->item($offset);
    }
    /**
     * @param int $offset
     * @param mixed $value
     * @return AbstractNodeListContainer
     */
    public function offsetSet($offset, $value)
    {
        return $this;
    }
    /**
     * @param int $offset
     * @return AbstractNodeListContainer
     */
    public function offsetUnset($offset)
    {
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
        return $this->offset;
    }
    /**
     * @return bool
     */
    public function valid()
    {
        return $this->offsetExists($this->offset);
    }
    /**
     * @return AbstractNodeListContainer
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
        return $this->nodeList->length;
    }
    /**
     * Must return the object class name that this container is made to contain
     */
    abstract protected function objectClass();
    /**
     * @param string $key
     * @param string $value
     * @return AbstractNodeHandler
     */
    public function get($value, $key)
    {
        if ($this->count() > 0) {
            $cacheValues = array(
                'class'        => get_called_class(),
                'object_class' => $this->objectClass(),
                'value'        => $value,
                'key'          => $key,
                'object'       => spl_object_hash($this),
            );
            $cachedModel = self::getCache($cacheValues);
            if ($cachedModel === null) {
                foreach ($this->nodeList as $node) {
                    $object = $this->domDocument->getHandler($node);
                    $get = sprintf('get%s', ucfirst($key));
                    if (!method_exists($object, $get)) {
                        throw new \InvalidArgumentException(sprintf('Property "%s" does not exist or its getter does not exist', $key));
                    }
                    $propertyValue = call_user_func(array(
                        $object,
                        $get,
                    ));
                    if ($value === $propertyValue) {
                        self::setCache($cacheValues, $object);
                        return $object;
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
     * @return AbstractNodeHandler|null
     */
    public function getAs(array $properties)
    {
        if ($this->count() > 0) {
            $cacheValues = array(
                'class'        => get_called_class(),
                'object_class' => $this->objectClass(),
                'properties'   => $properties,
                'object'       => spl_object_hash($this),
            );
            $cachedModel = self::getCache($properties);
            if ($cachedModel === null) {
                foreach ($this->nodeList as $node) {
                    $object = $this->domDocument->getHandler($node);
                    $same = true;
                    foreach ($properties as $name=>$value) {
                        $get = sprintf('get%s', ucfirst($name));
                        if (!method_exists($object, $get)) {
                            throw new \InvalidArgumentException(sprintf('Property "%s" does not exist or its getter does not exist', $name));
                        }
                        $propertyValue = call_user_func(array(
                            $object,
                            $get,
                        ));
                        $same &= $propertyValue === $value;
                    }
                    if ((bool)$same === true) {
                        self::setCache($cacheValues, $object);
                        return $object;
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
     * @return AbstractNodeListContainer
     */
    private static function purgeCache(array $values)
    {
        if (self::getCache($values)) {
            unset(self::$cache[self::cacheKey($values)]);
        }
    }
    /**
     * @param array $values
     * @return AbstractNodeListContainer
     */
    public static function purgeAllCache()
    {
        self::$cache = array();
    }
    /**
     * @param array $values
     * @param mixed $value
     * @return AbstractNodeListContainer
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
        return sprintf('_%s', json_encode($values));
    }
}
