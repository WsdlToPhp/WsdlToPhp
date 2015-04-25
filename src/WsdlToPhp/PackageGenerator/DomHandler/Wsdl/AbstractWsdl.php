<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\ElementHandler;
use WsdlToPhp\PackageGenerator\DomHandler\DomDocumentHandler;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler;

class AbstractWsdl extends DomDocumentHandler
{
    const
        TAG_ALL             = 'all',
        TAG_ANNOTATION      = 'annotation',
        TAG_ANY             = 'any',
        TAG_ANY_ATTRIBUTE   = 'anyAttribute',
        TAG_APPINFO         = 'appinfo',
        TAG_ATTRIBUTE       = 'attribute',
        TAG_ATTRIBUTE_GROUP = 'attributeGroup',
        TAG_BODY            = 'body',
        TAG_CHOICE          = 'choice',
        TAG_COMPLEX_CONTENT = 'complexContent',
        TAG_COMPLEX_TYPE    = 'complexType',
        TAG_DOCUMENTATION   = 'documentation',
        TAG_ELEMENT         = 'element',
        TAG_ENUMERATION     = 'enumeration',
        TAG_EXTENSION       = 'extension',
        TAG_FIELD           = 'field',
        TAG_GROUP           = 'group',
        TAG_HEADER          = 'header',
        TAG_IMPORT          = 'import',
        TAG_INCLUDE         = 'include',
        TAG_INPUT           = 'input',
        TAG_KEY             = 'key',
        TAG_KEYREF          = 'keyref',
        TAG_LIST            = 'list',
        TAG_MEMBER_TYPES    = 'memberTypes',
        TAG_MESSAGE         = 'message',
        TAG_NOTATION        = 'notation',
        TAG_OPERATION       = 'operation',
        TAG_OUTPUT          = 'output',
        TAG_PART            = 'part',
        TAG_REDEFINE        = 'redefine',
        TAG_RESTRICTION     = 'restriction',
        TAG_SELECTOR        = 'selector',
        TAG_SEQUENCE        = 'sequence',
        TAG_SCHEMA          = 'schema',
        TAG_SIMPLE_CONTENT  = 'simpleContent',
        TAG_SIMPLE_TYPE     = 'simpleType',
        TAG_UNION           = 'union',
        TAG_UNIQUE          = 'unique';
    /**
     * @var string
     */
    protected $currentTag;
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler::getElementHandler()
     * @return ElementHandler
     */
    protected function getElementHandler(\DOMElement $element, AbstractDomDocumentHandler $domDocument, $index = -1)
    {
        $handlerName = '\\WsdlToPhp\\PackageGenerator\\DomHandler\\ElementHandler';
        $tagClass    = sprintf('%s\\Tag\\Tag%s', __NAMESPACE__, ucfirst($this->currentTag));
        if (class_exists($tagClass)) {
            $handlerName = $tagClass;
        }
        return new $handlerName($element, $domDocument, $index);
    }
    /**
     * @param array $tags
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAbstractElement]
     */
    protected function getElementsByTags(array $tags)
    {
        $elements = array();
        foreach ($tags as $tag) {
            $this->currentTag = $tag;
            $elements = array_merge($elements, $this->getElementsByName($tag));
        }
        $this->currentTag = null;
        return $elements;
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagImport]
     */
    public function getImports()
    {
        return $this->getElementsByTags(array(
            self::TAG_IMPORT,
            self::TAG_INCLUDE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagComplex]
     */
    public function getComplexTypes()
    {
        return $this->getElementsByTags(array(
            self::TAG_COMPLEX_TYPE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagSimple]
     */
    public function getSimpleTypes()
    {
        return $this->getElementsByTags(array(
            self::TAG_SIMPLE_TYPE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagElement]
     */
    public function getElements()
    {
        return $this->getElementsByTags(array(
            self::TAG_ELEMENT,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagRestriction]
     */
    public function getRestrictions()
    {
        return $this->getElementsByTags(array(
            self::TAG_RESTRICTION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagEnumeration]
     */
    public function getEnumerations()
    {
        return $this->getElementsByTags(array(
            self::TAG_ENUMERATION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagInput]
     */
    public function getInputs()
    {
        return $this->getElementsByTags(array(
            self::TAG_INPUT,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagOuput]
     */
    public function getOutputs()
    {
        return $this->getElementsByTags(array(
            self::TAG_OUTPUT,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagBodies]
     */
    public function getBodies()
    {
        return $this->getElementsByTags(array(
            self::TAG_BODY,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagBodies]
     */
    public function getHeaders()
    {
        return $this->getElementsByTags(array(
            self::TAG_HEADER,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagMessage]
     */
    public function getMessages()
    {
        return $this->getElementsByTags(array(
            self::TAG_MESSAGE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagPart]
     */
    public function getParts()
    {
        return $this->getElementsByTags(array(
            self::TAG_PART,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagOperation]
     */
    public function getOperations()
    {
        return $this->getElementsByTags(array(
            self::TAG_OPERATION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAttribute]
     */
    public function getAttributes()
    {
        return $this->getElementsByTags(array(
            self::TAG_ATTRIBUTE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagDocumentation]
     */
    public function getDocumentations()
    {
        return $this->getElementsByTags(array(
            self::TAG_DOCUMENTATION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagExtension]
     */
    public function getExtensions()
    {
        return $this->getElementsByTags(array(
            self::TAG_EXTENSION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagList]
     */
    public function getLists()
    {
        return $this->getElementsByTags(array(
            self::TAG_LIST,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagUnion]
     */
    public function getUnions()
    {
        return $this->getElementsByTags(array(
            self::TAG_UNION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagComplexContent]
     */
    public function getComplexContents()
    {
        return $this->getElementsByTags(array(
            self::TAG_COMPLEX_CONTENT,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagSimpleContent]
     */
    public function getSimpleContents()
    {
        return $this->getElementsByTags(array(
            self::TAG_SIMPLE_CONTENT,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagSequence]
     */
    public function getSequences()
    {
        return $this->getElementsByTags(array(
            self::TAG_SEQUENCE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAll]
     */
    public function getAlls()
    {
        return $this->getElementsByTags(array(
            self::TAG_ALL,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAnnotation]
     */
    public function getAnnotations()
    {
        return $this->getElementsByTags(array(
            self::TAG_ANNOTATION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAny]
     */
    public function getAnys()
    {
        return $this->getElementsByTags(array(
            self::TAG_ANY,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAnyAttribute]
     */
    public function getAnyAttributes()
    {
        return $this->getElementsByTags(array(
            self::TAG_ANY_ATTRIBUTE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAppinfo]
     */
    public function getAppinfos()
    {
        return $this->getElementsByTags(array(
            self::TAG_APPINFO,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagAttributeGroup]
     */
    public function getAttributeGroups()
    {
        return $this->getElementsByTags(array(
            self::TAG_ATTRIBUTE_GROUP,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagChoice]
     */
    public function getChoices()
    {
        return $this->getElementsByTags(array(
            self::TAG_CHOICE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagField]
     */
    public function getFields()
    {
        return $this->getElementsByTags(array(
            self::TAG_FIELD,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagGroup]
     */
    public function getGroups()
    {
        return $this->getElementsByTags(array(
            self::TAG_GROUP,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagKey]
     */
    public function getKeys()
    {
        return $this->getElementsByTags(array(
            self::TAG_KEY,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagKeyref]
     */
    public function getKeyrefs()
    {
        return $this->getElementsByTags(array(
            self::TAG_KEYREF,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagNotation]
     */
    public function getNotations()
    {
        return $this->getElementsByTags(array(
            self::TAG_NOTATION,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagRedefine]
     */
    public function getRedefines()
    {
        return $this->getElementsByTags(array(
            self::TAG_REDEFINE,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagSchema]
     */
    public function getSchemas()
    {
        return $this->getElementsByTags(array(
            self::TAG_SCHEMA,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagSelector]
     */
    public function getSelectors()
    {
        return $this->getElementsByTags(array(
            self::TAG_SELECTOR,
        ));
    }
    /**
     * @return array[WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagUnique]
     */
    public function getUniques()
    {
        return $this->getElementsByTags(array(
            self::TAG_UNIQUE,
        ));
    }
}
