<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\ElementHandler;
use WsdlToPhp\PackageGenerator\DomHandler\DomDocumentHandler;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler;

class AbstractWsdl extends DomDocumentHandler
{
    const
        TAG_LIST           = 'list',
        TAG_PART           = 'part',
        TAG_BODY           = 'body',
        TAG_UNION          = 'union',
        TAG_INPUT          = 'input',
        TAG_IMPORT         = 'import',
        TAG_HEADER         = 'header',
        TAG_OUTPUT         = 'output',
        TAG_INCLUDE        = 'include',
        TAG_ELEMENT        = 'element',
        TAG_MESSAGE        = 'message',
        TAG_OPERATION      = 'operation',
        TAG_ATTRIBUTE      = 'attribute',
        TAG_EXTENSION      = 'extension',
        TAG_SIMPLE_TYPE    = 'simpleType',
        TAG_ENUMERATION    = 'enumeration',
        TAG_RESTRICTION    = 'restriction',
        TAG_COMPLEX_TYPE   = 'complexType',
        TAG_DOCUMENTATION  = 'documentation';
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
        $handlerName = '';
        switch ($this->currentTag) {
            case self::TAG_COMPLEX_TYPE:
            case self::TAG_SIMPLE_TYPE:
                $handlerName = sprintf('%s\\Tag%s',
                                        __NAMESPACE__,
                                        ucfirst(strtolower(str_replace('Type', '', $this->currentTag))));
                break;
            case self::TAG_BODY:
            case self::TAG_PART:
            case self::TAG_INPUT:
            case self::TAG_UNION:
            case self::TAG_HEADER:
            case self::TAG_IMPORT:
            case self::TAG_OUTPUT:
            case self::TAG_ELEMENT:
            case self::TAG_INCLUDE:
            case self::TAG_MESSAGE:
            case self::TAG_ATTRIBUTE:
            case self::TAG_OPERATION:
            case self::TAG_RESTRICTION:
            case self::TAG_ENUMERATION:
            case self::TAG_DOCUMENTATION:
                $handlerName = sprintf('%s\\Tag%s', __NAMESPACE__, ucfirst(strtolower($this->currentTag)));
                break;
            default:
                $handlerName = '\\WsdlToPhp\\PackageGenerator\\DomHandler\\ElementHandler';
                break;
        }
        if (!empty($handlerName)) {
            return new $handlerName($element, $domDocument, $index);
        }
        return null;
    }
    /**
     * @param array $tags
     * @return array[TagAbstractElement]
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
     * @return array[TagImport]
     */
    public function getImports()
    {
        return $this->getElementsByTags(array(
            self::TAG_IMPORT,
            self::TAG_INCLUDE,
        ));
    }
    /**
     * @return array[TagComplex]
     */
    public function getComplexTypes()
    {
        return $this->getElementsByTags(array(
            self::TAG_COMPLEX_TYPE,
        ));
    }
    /**
     * @return array[TagSimple]
     */
    public function getSimpleTypes()
    {
        return $this->getElementsByTags(array(
            self::TAG_SIMPLE_TYPE,
        ));
    }
    /**
     * @return array[TagElement]
     */
    public function getElements()
    {
        return $this->getElementsByTags(array(
            self::TAG_ELEMENT,
        ));
    }
    /**
     * @return array[TagRestriction]
     */
    public function getRestrictions()
    {
        return $this->getElementsByTags(array(
            self::TAG_RESTRICTION,
        ));
    }
    /**
     * @return array[TagEnumeration]
     */
    public function getEnumerations()
    {
        return $this->getElementsByTags(array(
            self::TAG_ENUMERATION,
        ));
    }
    /**
     * @return array[TagInput]
     */
    public function getInputs()
    {
        return $this->getElementsByTags(array(
            self::TAG_INPUT,
        ));
    }
    /**
     * @return array[TagOuput]
     */
    public function getOutputs()
    {
        return $this->getElementsByTags(array(
            self::TAG_OUTPUT,
        ));
    }
    /**
     * @return array[TagBodies]
     */
    public function getBodies()
    {
        return $this->getElementsByTags(array(
            self::TAG_BODY,
        ));
    }
    /**
     * @return array[TagBodies]
     */
    public function getHeaders()
    {
        return $this->getElementsByTags(array(
            self::TAG_HEADER,
        ));
    }
    /**
     * @return array[TagMessage]
     */
    public function getMessages()
    {
        return $this->getElementsByTags(array(
            self::TAG_MESSAGE,
        ));
    }
    /**
     * @return array[TagPart]
     */
    public function getParts()
    {
        return $this->getElementsByTags(array(
            self::TAG_PART,
        ));
    }
    /**
     * @return array[TagOperation]
     */
    public function getOperations()
    {
        return $this->getElementsByTags(array(
            self::TAG_OPERATION,
        ));
    }
    /**
     * @return array[TagAttribute]
     */
    public function getAttributes()
    {
        return $this->getElementsByTags(array(
            self::TAG_ATTRIBUTE,
        ));
    }
    /**
     * @return array[TagDocumentation]
     */
    public function getDocumentations()
    {
        return $this->getElementsByTags(array(
            self::TAG_DOCUMENTATION,
        ));
    }
    /**
     * @return array[TagExtension]
     */
    public function getExtensions()
    {
        return $this->getElementsByTags(array(
            self::TAG_EXTENSION,
        ));
    }
    /**
     * @return array[TagList]
     */
    public function getLists()
    {
        return $this->getElementsByTags(array(
            self::TAG_LIST,
        ));
    }
    /**
     * @return array[TagUnion]
     */
    public function getunions()
    {
        return $this->getElementsByTags(array(
            self::TAG_UNION,
        ));
    }
}
