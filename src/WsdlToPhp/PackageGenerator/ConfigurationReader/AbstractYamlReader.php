<?php

namespace WsdlToPhp\PackageGenerator\ConfigurationReader;

use Symfony\Component\Yaml\Parser;

abstract class AbstractYamlReader
{
    /**
     * @var Options
     */
    private static $instance;
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
     * @return array
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
        if (!isset(self::$instance)) {
            if (empty($filename) || !is_file($filename)) {
                throw new \InvalidArgumentException(sprintf('Unable to locate file "%s"', $filename));
            }
            self::$instance = new static($filename);
        }
        return self::$instance;
    }
}
