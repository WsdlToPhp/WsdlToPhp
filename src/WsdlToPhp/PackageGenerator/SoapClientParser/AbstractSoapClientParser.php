<?php

namespace WsdlToPhp\PackageGenerator\SoapClientParser;

use WsdlToPhp\PackageGenerator\Generator\Generator;

abstract class AbstractSoapClientParser implements SoapClientParserInterface
{
    /**
     * @var Generator
     */
    protected $generator;
    /**
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }
}
