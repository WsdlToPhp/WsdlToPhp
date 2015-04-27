<?php

namespace WsdlToPhp\PackageGenerator\WsdlParser;

use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlContent;
use WsdlToPhp\PackageGenerator\Generator\ParserInterface;
use WsdlToPhp\PackageGenerator\Generator\Generator;

abstract class AbstractParser implements ParserInterface
{
    /**
     * @var Generator
     */
    protected $generator;
    /**
     * @var bool
     */
    protected $continue;
    /**
     * @var array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl]
     */
    protected $tags;
    /**
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
        $this->continue  = false;
    }
    /**
     * The method takes care of looping among WSDLS as much time as it is needed
     * @see \WsdlToPhp\PackageGenerator\Generator\ParserInterface::parse()
     */
    public function parse()
    {
        if ($this->generator->getWsdls()->count() > 0) {
            do {
                foreach ($this->generator->getWsdls() as $wsdl) {
                    $content = $wsdl->getContent();
                    $this->setTags($content->getElementsByName($this->parsingTag()));
                    if ($content instanceof WsdlContent && count($this->getTags()) > 0) {
                        $this->parseWsdl($wsdl);
                    }
                }
            } while($this->shouldContinue());
        }
    }
    /**
     * Actual parsing of the sdl/schema
     * @param Wsdl $wsdl
     */
    abstract protected function parseWsdl(Wsdl $wsdl);
    /**
     * Must return the tag that will be parsed
     * @return string
     */
    abstract protected function parsingTag();
    /**
     * When looping, must return false to stop it
     * @return bool
     */
    protected function shouldContinue()
    {
        return $this->continue;
    }
    /**
     * @param bool $continue;
     * @return AbstractParser
     */
    protected function setContinue($continue)
    {
        $this->continue = $continue;
    }
    /**
     * @param array $tags
     * @return \WsdlToPhp\PackageGenerator\WsdlParser\AbstractParser
     */
    private function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }
    /**
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl]
     */
    protected function getTags()
    {
        return $this->tags;
    }
}
