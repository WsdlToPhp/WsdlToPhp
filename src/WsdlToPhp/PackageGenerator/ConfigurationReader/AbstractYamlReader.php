<?php

namespace WsdlToPhp\PackageGenerator\ConfigurationReader;

use Symfony\Component\Yaml\Parser;

abstract class AbstractYamlReader
{
    /**
     * @var AbstractYamlReader[]
     */
    private static $instances;
    /**
     * Path to file to parse
     * @var string
     */
    protected $filename;
    /**
     * Use singleton, instead of calling new Options(), use Options::instance()
     * @param string options's file to parse
     */
    abstract protected function __construct($filename);
    /**
     * @param string $filename
     * @return mixed
     */
    protected function loadYaml($filename)
    {
        $ymlParser = new Parser();
        return $ymlParser->parse(file_get_contents($filename));
    }
    /**
     * @param string options's file to parse
     * @return \WsdlToPhp\Generator\Options
     */
    public static function instance($filename = null)
    {
        if (empty($filename) || !is_file($filename)) {
            throw new \InvalidArgumentException(sprintf('Unable to locate file "%s"', $filename), __LINE__);
        }
        $key = sprintf('%s_%s', get_called_class(), $filename);
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new static($filename);
        }
        return self::$instances[$key];
    }
    /**
     * @param string $filename
     * @param string $mainKey
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function parseSimpleArray($filename, $mainKey)
    {
        $values = $this->loadYaml($filename);
        if (!array_key_exists($mainKey, $values)) {
            throw new \InvalidArgumentException(sprintf('Unable to find section "%s" in "%s"', $mainKey, $filename), __LINE__);
        }
        return $values[$mainKey];
    }
}
