<?php

namespace WsdlToPhp\PackageGenerator\Generator;

use Symfony\Component\Yaml\Parser;

abstract class AbstractOptions
{
    /**
     * Common values used as option's value
     */
    const
        VALUE_START = 'start',
        VALUE_END   = 'end',
        VALUE_NONE  = 'none',
        VALUE_CAT   = 'cat',
        VALUE_TRUE  = true,
        VALUE_FALSE = false;
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
            $parseFilename = empty($filename) ? dirname(__FILE__) . '/../Resources/config/generator_options.yml' : $filename;
            if (!is_file($parseFilename)) {
                throw new \InvalidArgumentException(sprintf('Unable to locate file "%s"', $parseFilename));
            }
            self::$instance = new static($parseFilename);
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
            throw new \InvalidArgumentException(sprintf('Invalid option value "%s", possible values: %s', $optionValue, implode(', ', $this->options[$optionName]['values'])));
        } else {
            $this->options[$optionName]['value'] = $optionValue;
        }
        return $this;
    }
}
