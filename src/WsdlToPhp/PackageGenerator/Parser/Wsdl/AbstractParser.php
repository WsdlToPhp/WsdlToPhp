<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Generator\ParserInterface;
use WsdlToPhp\PackageGenerator\Generator\Generator;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler as Attribute;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\AbstractTag as Tag;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagRestriction as Restriction;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagEnumeration as Enumeration;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAnnotation as Annotation;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAppinfo as Appinfo;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagDocumentation as Documentation;
use WsdlToPhp\PackageGenerator\Model\AbstractModel as Model;

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
     * @return Generator
     */
    public function getGenerator()
    {
        return $this->generator;
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
     * Must return the model on which the method will be called
     * @param Tag $tag
     * @return Model
     */
    abstract protected function getModel(Tag $tag);
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
        return array_key_exists($wsdl->getName(), $this->parsedWsdls) &&
                is_array($this->parsedWsdls[$wsdl->getName()]) &&
                in_array($this->parsingTag(), $this->parsedWsdls[$wsdl->getName()]);
    }
    /**
     * @param Tag $tag
     */
    protected function parseRestrictions(Tag $tag)
    {
        $restrictions = $tag->getChildrenByName(WsdlDocument::TAG_RESTRICTION);
        foreach ($restrictions as $restriction) {
            if ($restriction->isEnumeration() === false) {
                $this->parseRestriction($tag, $restriction);
            } else {
                $this->parseEnumeration($tag, $restriction);
            }
        }
    }
    /**
     * @param Tag $tag
     * @param Restriction $restriction
     */
    private function parseRestriction(Tag $tag, Restriction $restriction)
    {
        if ($restriction->hasAttributes()) {
            foreach ($restriction->getAttributes() as $attribute) {
                $this->addMetaFromAttribute($tag, $attribute);
            }
        }
        foreach ($restriction->getChildren() as $child) {
            $this->parseRestrictionChild($tag, $child);
        }
    }
    /**
     * @param Tag $tag
     * @param Attribute $attribute
     */
    private function addMetaFromAttribute(Tag $tag, Attribute $attribute)
    {
        if ($this->getModel($tag) !== null) {
            $this->getModel($tag)->addMeta($attribute->getName(), $attribute->getValue());
        }
    }
    /**
     * @param Tag $tag
     * @param Tag $child
     */
    private function parseRestrictionChild(Tag $tag, Tag $child)
    {
        if ($child->hasAttribute(Attribute::ATTRIBUTE_VALUE) && $this->getModel($tag) !== null) {
            $this->getModel($tag)->addMeta($child->getName(), $child->getAttributeValue());
        }
    }
    /**
     * @param Tag $tag
     * @param Restriction $restriction
     */
    private function parseEnumeration(Tag $tag, Restriction $restriction)
    {
        foreach ($restriction->getEnumerations() as $enumeration) {
            $this->addStructValue($tag, $enumeration);
        }
    }
    /**
     * @param Tag $tag
     * @param Enumeration $enumeration
     */
    private function addStructValue(Tag $tag, Enumeration $enumeration)
    {
        $this->generator->addRestrictionValue($tag->getAttributeName(), $enumeration->getValue());
    }
    /**
     * @param Tag $tag
     */
    protected function parseAnnotations(Tag $tag)
    {
        $annotations = $tag->getChildrenByName(WsdlDocument::TAG_ANNOTATION);
        foreach ($annotations as $annotation) {
            $this->parseAnnotation($tag, $annotation);
        }
    }
    /**
     * @param Annotation $annotation
     */
    private function parseAnnotation(Tag $tag, Annotation $annotation)
    {
        $appinfos = $annotation->getChildrenByName(WsdlDocument::TAG_APPINFO);
        foreach ($appinfos as $appinfo) {
            $this->parseAppinfo($tag, $appinfo);
        }
        $documentations = $annotation->getChildrenByName(WsdlDocument::TAG_DOCUMENTATION);
        foreach ($documentations as $documentation) {
            $this->parseDocumentation($tag, $documentation);
        }
    }
    /**
     * @param Tag $tag
     * @param Appinfo $appinfo
     */
    private function parseAppinfo(Tag $tag, Appinfo $appinfo)
    {
        $content = $appinfo->getContent();
        if (!empty($content) && $this->getModel($tag) !== null) {
            $this->getModel($tag)->addMeta('appinfo', $content);
        }
    }
    /**
     * @param Tag $tag
     * @param Documentation $documentation
     */
    private function parseDocumentation(Tag $tag, Documentation $documentation)
    {
        $content = $documentation->getContent();
        if (!empty($content) && $this->getModel($tag) !== null) {
            $this->getModel($tag)->setDocumentation($content);
        }
    }
}
