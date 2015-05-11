<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\AbstractAttributeHandler as Attribute;

class TagHeader extends AbstractTag
{
    const
        ATTRIBUTE_MESSAGE  = 'message',
        ATTRIBUTE_PART     = 'part',
        ATTRIBUTE_REQUIRED = 'required',

        REQUIRED_HEADER    = 'required',
        OPTIONAL_HEADER    = 'optional';
    /**
     * @return TagOperation|null
     */
    public function getParentOperation()
    {
        $input = $this->getParentInput();
        if ($input !== null) {
            return $input->getParentOperation();
        }
        return null;
    }
    /**
     * @return TagInput|null
     */
    public function getParentInput()
    {
        return $this->getStrictParent(WsdlDocument::TAG_INPUT);
    }
    /**
     * @return string
     */
    public function getAttributePart()
    {
        return $this->hasAttribute(self::ATTRIBUTE_PART) === true ? $this->getAttribute(self::ATTRIBUTE_PART)->getValue() : '';
    }
    /**
     * @return string
     */
    public function getAttributeMessage()
    {
        return $this->hasAttribute(self::ATTRIBUTE_MESSAGE) === true ? $this->getAttribute(self::ATTRIBUTE_MESSAGE)->getValue() : '';
    }
    /**
     * @return string
     */
    public function getAttributeMessageNamespace()
    {
        return $this->hasAttribute(self::ATTRIBUTE_MESSAGE) === true ? $this->getAttribute(self::ATTRIBUTE_MESSAGE)->getValueNamespace() : '';
    }
    /**
     * @return string
     */
    public function getAttributeRequired()
    {
        return $this->hasAttribute(self::ATTRIBUTE_REQUIRED) === true ? $this->getAttribute(self::ATTRIBUTE_REQUIRED)->getValue(true, 'bool') : '';
    }
    /**
     * @return string
     */
    public function getAttributeNamespace()
    {
        return $this->hasAttribute(Attribute::ATTRIBUTE_NAMESPACE) === true ? $this->getAttribute(Attribute::ATTRIBUTE_NAMESPACE)->getValue() : '';
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagMessage
     */
    public function getMessage()
    {
        $messageName = $this->getAttributeMessage();
        if (!empty($messageName)) {
            return $this->getDomDocumentHandler()->getElementByNameAndAttributes('message', array(
                'name' => $messageName,
            ));
        }
        return null;
    }
    /**
     * @return null|\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagPart
     */
    public function getPart()
    {
        $message  = $this->getMessage();
        $partName = $this->getAttributePart();
        if ($message !== null && !empty($partName)) {
            return $message->getPart($partName);
        }
        return null;
    }
    /**
     * @return string
     */
    public function getHeaderType()
    {
        $part = $this->getPart();
        return $part !== null ? $part->getFinalType() : '';
    }
    /**
     * @return string
     */
    public function getHeaderName()
    {
        $part = $this->getPart();
        return $part !== null ? $part->getFinalName() : '';
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractNodeHandler::getNamespace()
     * @return string
     */
    public function getHeaderNamespace()
    {
        $messageNamespace = $this->getAttributeMessageNamespace();
        if (empty($messageNamespace) || ($namespace = $this->getDomDocumentHandler()->getNamespaceUri($messageNamespace)) === '') {
            $part      = $this->getPart();
            $namespace = '';
            if ($part !== null) {
                $finalNamespace = $part->getFinalNamespace();
                if (!empty($finalNamespace)) {
                    $namespace = $this->getDomDocumentHandler()->getNamespaceUri($finalNamespace);
                }
            }
        }
        return $namespace;
    }
    /**
     * @return string
     */
    public function getHeaderRequired()
    {
        return $this->getAttributeRequired() === true ? self::REQUIRED_HEADER : self::OPTIONAL_HEADER;
    }
}
