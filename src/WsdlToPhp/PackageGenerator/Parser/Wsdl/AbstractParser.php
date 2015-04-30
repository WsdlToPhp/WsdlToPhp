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
use WsdlToPhp\PackageGenerator\Model\Struct;
use WsdlToPhp\PackageGenerator\Model\Method;

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
     * Return the model on which the method will be called
     * @param Tag $tag
     * @return Struct|Method
     */
    protected function getModel(Tag $tag)
    {
        $model = null;
        switch ($tag->getName()) {
            case WsdlDocument::TAG_OPERATION:
                $model = $this->generator->getServiceMethod($tag->getAttributeName());
                break;
            default:
                $model = $this->generator->getStruct($tag->getAttributeName());
                break;
        }
        return $model;
    }
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
    /**
     * @param Tag $tag
     */
    protected function parseRestrictions(Tag $tag)
    {
        $restrictions = $tag->getChildrenByName(WsdlDocument::TAG_RESTRICTION);
        foreach ($restrictions as $restriction) {
            if ($restriction->isEnumeration() === false) {
                $this->parseRestriction($restriction);
            } else {
                $this->parseEnumeration($restriction);
            }
        }
    }
    /**
     * @param Tag $tag
     * @param Restriction $restriction
     */
    private function parseRestriction(Restriction $restriction)
    {
        $parent = $restriction->getSuitableParent();
        if ($parent !== null) {
            $this->getGenerator()->getStructs()->addVirtualStruct($parent->getAttributeName());

            if ($restriction->hasAttributes()) {
                foreach ($restriction->getAttributes() as $attribute) {
                    if ($attribute->getName() === 'base' && $attribute->getValue() !== $parent->getAttributeName()) {
                        if ($this->getModel($parent) !== null) {
                            $this->getModel($parent)->setInheritance($attribute->getValue());
                        }
                    } else {
                        $this->addMetaFromAttribute($parent, $attribute);
                    }
                }
            }

            foreach ($restriction->getElementChildren() as $child) {
                $this->parseRestrictionChild($parent, $child);
            }
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
    private function parseEnumeration(Restriction $restriction)
    {
        $parent = $restriction->getSuitableParent();
        if ($parent !== null) {
            foreach ($restriction->getEnumerations() as $enumeration) {
                $this->addStructValue($parent, $enumeration);
            }
        }
    }
    /**
     * @param Tag $tag
     * @param Enumeration $enumeration
     */
    private function addStructValue(Tag $tag, Enumeration $enumeration)
    {
        $struct = $this->getModel($tag);
        if ($struct instanceof  Struct) {
            $struct->addValue($enumeration->getValue());
        }
    }
    /**
     * @param Tag $tag
     */
    protected function parseAnnotations(Tag $tag)
    {
        $annotations = $tag->getChildrenByName(WsdlDocument::TAG_ANNOTATION);
        foreach ($annotations as $annotation) {
            $this->parseAnnotation($annotation);
        }
    }
    /**
     * @param Annotation $annotation
     */
    private function parseAnnotation(Annotation $annotation)
    {
        $appinfos = $annotation->getChildrenByName(WsdlDocument::TAG_APPINFO);
        foreach ($appinfos as $appinfo) {
            $this->parseAppinfo($appinfo);
        }
        $documentations = $annotation->getChildrenByName(WsdlDocument::TAG_DOCUMENTATION);
        foreach ($documentations as $documentation) {
            $this->parseDocumentation($documentation);
        }
    }
    /**
     * @param Appinfo $appinfo
     */
    private function parseAppinfo(Appinfo $appinfo)
    {
        $content = $appinfo->getContent();
        $parent  = $appinfo->getSuitableParent();
        if (!empty($content) && $parent !== null && $this->getModel($parent->getAttributeName()) !== null) {
            $this->getModel($parent->getAttributeName())->addMeta('appinfo', $content);
        }
    }
    /**
     * @param Documentation $documentation
     */
    protected function parseDocumentation(Documentation $documentation)
    {
        $content      = $documentation->getContent();
        $parent       = $documentation->getSuitableParent();
        $parentParent = $parent !== null ? $parent->getSuitableParent() : null;

        if (!empty($content) && $parent !== null) {
            /**
             * Is it an element ? part of a struct
             * Finds parent node of this documentation node
             */
            if ($parent->hasAttribute('type') && $parentParent !== null) {
                if ($this->getModel($parentParent) instanceof Struct && $this->getModel($parentParent)->getAttribute($parent->getAttributeName())) {
                    $this->getModel($parentParent)->getAttribute($parent->getAttributeName())->setDocumentation($content);
                }
            } elseif($parent->getName() === WsdlDocument::TAG_ENUMERATION) {
                if ($parentParent !== null && $this->getModel($parentParent) !== null && $this->getModel($parentParent)->getValue($parent->getAttributeName()) !== null) {
                    $this->getModel($parentParent)->getValue($parent->getAttributeName())->setDocumentation($content);
                }
            } elseif ($this->getModel($parent) !== null) {
                $this->getModel($parent)->setDocumentation($content);
            }
        }
    }
}
