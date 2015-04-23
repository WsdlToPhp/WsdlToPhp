<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\ElementHandler;
use WsdlToPhp\PackageGenerator\DomHandler\DomDocumentHandler;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler;

class AbstractWsdl extends DomDocumentHandler
{
    const
        TAG_PART         = 'part',
        TAG_BODY         = 'body',
        TAG_INPUT        = 'input',
        TAG_IMPORT       = 'import',
        TAG_HEADER       = 'header',
        TAG_OUTPUT       = 'output',
        TAG_INCLUDE      = 'include',
        TAG_ELEMENT      = 'element',
        TAG_MESSAGE      = 'message',
        TAG_OPERATION    = 'operation',
        TAG_ATTRIBUTE    = 'attribute',
        TAG_SIMPLE_TYPE  = 'simpleType',
        TAG_ENUMERATION  = 'enumeration',
        TAG_RESTRICTION  = 'restriction',
        TAG_COMPLEX_TYPE = 'complexType';
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
            case self::TAG_INCLUDE:
            case self::TAG_IMPORT:
                $handlerName = __NAMESPACE__ . '\\Import';
                break;
            case self::TAG_COMPLEX_TYPE:
            case self::TAG_SIMPLE_TYPE:
                $handlerName = sprintf('%s\\%s',
                                        __NAMESPACE__,
                                        ucfirst(strtolower(str_replace('Type', '', $this->currentTag))));
                break;
            case self::TAG_BODY:
            case self::TAG_PART:
            case self::TAG_INPUT:
            case self::TAG_HEADER:
            case self::TAG_OUTPUT:
            case self::TAG_ELEMENT:
            case self::TAG_MESSAGE:
            case self::TAG_ATTRIBUTE:
            case self::TAG_OPERATION:
            case self::TAG_RESTRICTION:
            case self::TAG_ENUMERATION:
                $handlerName = sprintf('%s\\%s', __NAMESPACE__, ucfirst(strtolower($this->currentTag)));
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
     * @return array[AbstractElement]
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
     * @return array[Import]
     */
    public function getImports()
    {
        return $this->getElementsByTags(array(
            self::TAG_IMPORT,
            self::TAG_INCLUDE,
        ));
    }
    /**
     * @return array[Complex]
     */
    public function getComplexTypes()
    {
        return $this->getElementsByTags(array(
            self::TAG_COMPLEX_TYPE,
        ));
    }
    /**
     * @return array[Simple]
     */
    public function getSimpleTypes()
    {
        return $this->getElementsByTags(array(
            self::TAG_SIMPLE_TYPE,
        ));
    }
    /**
     * @return array[Element]
     */
    public function getElements()
    {
        return $this->getElementsByTags(array(
            self::TAG_ELEMENT,
        ));
    }
    /**
     * @return array[Restriction]
     */
    public function getRestrictions()
    {
        return $this->getElementsByTags(array(
            self::TAG_RESTRICTION,
        ));
    }
    /**
     * @return array[Enumeration]
     */
    public function getEnumerations()
    {
        return $this->getElementsByTags(array(
            self::TAG_ENUMERATION,
        ));
    }
    /**
     * @return array[Input]
     */
    public function getInputs()
    {
        return $this->getElementsByTags(array(
            self::TAG_INPUT,
        ));
    }
    /**
     * @return array[Ouput]
     */
    public function getOutputs()
    {
        return $this->getElementsByTags(array(
            self::TAG_OUTPUT,
        ));
    }
    /**
     * @return array[Bodies]
     */
    public function getBodies()
    {
        return $this->getElementsByTags(array(
            self::TAG_BODY,
        ));
    }
    /**
     * @return array[Bodies]
     */
    public function getHeaders()
    {
        return $this->getElementsByTags(array(
            self::TAG_HEADER,
        ));
    }
    /**
     * @return array[Message]
     */
    public function getMessages()
    {
        return $this->getElementsByTags(array(
            self::TAG_MESSAGE,
        ));
    }
    /**
     * @return array[Part]
     */
    public function getParts()
    {
        return $this->getElementsByTags(array(
            self::TAG_PART,
        ));
    }
    /**
     * @return array[Operation]
     */
    public function getOperations()
    {
        return $this->getElementsByTags(array(
            self::TAG_OPERATION,
        ));
    }
    /**
     * @return array[Attribute]
     */
    public function getAttributes()
    {
        return $this->getElementsByTags(array(
            self::TAG_ATTRIBUTE,
        ));
    }
}
