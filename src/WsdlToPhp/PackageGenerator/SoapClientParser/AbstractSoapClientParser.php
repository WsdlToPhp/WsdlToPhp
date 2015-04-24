<?php

namespace WsdlToPhp\PackageGenerator\Generator;
use WsdlToPhp\PackageGenerator\Generator\SoapClientParserInterface;

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
