<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Generator\ParserInterface;
use WsdlToPhp\PackageGenerator\Generator\Generator;

abstract class AbstractParser implements ParserInterface
{
    /**
     * @var Generator
     */
    protected $generator;
    /**
     * @var array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl]
     */
    protected $tags;
    /**
     * List of Wsdl parsed fr the current tag
     * @var unknown
     */
    private $parsedWsdls;
    /**
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator   = $generator;
        $this->continue    = false;
        $this->parsedWsdls = array();
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
                    if ($this->isWsdlParsed($wsdl) === false) {
                        $content = $wsdl->getContent();
                        if ($content !== null) {
                            $this->setTags($content->getElementsByName($this->parsingTag()));
                            if (count($this->getTags()) > 0) {
                                $this->parseWsdl($wsdl);
                            }
                        }
                        $this->setWsdlAsParsed($wsdl);
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
        $shouldContinue = false;
        foreach ($this->generator->getWsdls() as $wsdl) {
            $shouldContinue |= $this->isWsdlParsed($wsdl) === false;
        }
        return (bool)$shouldContinue;
    }
    /**
     * @param array $tags
     * @return \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser
     */
    private function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }
    /**
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl]
     */
    public function getTags()
    {
        return $this->tags;
    }
    /**
     * @param Wsdl $wsdl
     * @return AbstractParser
     */
    private function setWsdlAsParsed(Wsdl $wsdl)
    {
        if (!array_key_exists($wsdl->getName(), $this->parsedWsdls)) {
            $this->parsedWsdls[$wsdl->getName()] = array();
        }
        $this->parsedWsdls[$wsdl->getName()][] = $this->parsingTag();
        return $this;
    }
    /**
     * @param Wsdl $wsdl
     * @return boolean
     */
    public function isWsdlParsed(Wsdl $wsdl)
    {
        return
            array_key_exists($wsdl->getName(), $this->parsedWsdls) &&
            is_array($this->parsedWsdls[$wsdl->getName()]) &&
            in_array($this->parsingTag(), $this->parsedWsdls[$wsdl->getName()]);
    }
}
