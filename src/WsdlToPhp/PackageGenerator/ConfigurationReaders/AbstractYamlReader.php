<?php

namespace WsdlToPhp\PackageGenerator\ConfigurationReaders;

use Symfony\Component\Yaml\Parser;

abstract class AbstractYamlReader
{
    /**
     * @var Options
     */
    private static $instance;
    /**
     * Generator's options
     * @var array
     */
    protected $options;
    /**
     * Path to file to parse
     * @var string
     */
    protected $filename;
    /**
     * Use singleton, instead of calling new Options(), use Options::instance()
     * @param string options's file to parse
     */
    protected function __construct($filename)
    {
        $this->options = array();
        $this->parseOptions($filename);
    }
    /**
     * Parse options for generator
     * @param string options's file to parse
     */
    protected function parseOptions($filename)
    {
        $ymlParser = new Parser();
        $this->options = $ymlParser->parse(file_get_contents($filename));
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
    /**
     * Returns the option value
     * @throws InvalidArgumentException
     * @param string $optionName
     * @return string|bool
     */
    public function getOptionValue($optionName)
    {
        if (!isset($this->options[$optionName])) {
            throw new \InvalidArgumentException(sprintf('Invalid option name "%s", possible options: %s', $optionName, implode(', ', array_keys($this->options))));
        }
        return array_key_exists('value', $this->options[$optionName]) ? $this->options[$optionName]['value'] : $this->options[$optionName]['default'];
    }
    /**
     * Allows to add an option and set its value
     * @throws InvalidArgumentException
     * @param string $optionName
     * @return \WsdlToPhp\Generator\Options
     */
    public function setOptionValue($optionName, $optionValue, array $values = array())
    {
        if (!isset($this->options[$optionName])) {
            $this->options[$optionName] = array(
                'value'  => $optionValue,
                'values' => $values,
            );
        } elseif(!empty($this->options[$optionName]['values']) && !in_array($optionValue, $this->options[$optionName]['values'], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for option "%s", possible values: %s', $optionValue, $optionName, implode(', ', $this->options[$optionName]['values'])));
        } else {
            $this->options[$optionName]['value'] = $optionValue;
        }
        return $this;
    }
}
