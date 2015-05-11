<?php

namespace WsdlToPhp\PackageGenerator\Parser\Wsdl;

use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagHeader as Header;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\Schema;

class TagHeader extends AbstractTagParser
{
    const
        META_SOAP_HEADERS           = 'SOAPHeaders',
        META_SOAP_HEADER_NAMES      = 'SOAPHeaderNames',
        META_SOAP_HEADER_TYPES      = 'SOAPHeaderTypes',
        META_SOAP_HEADER_NAMESPACES = 'SOAPHeaderNamespaces';
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::getTags()
     * @return array[\WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Tag\TagHeader]
     */
    public function getTags()
    {
        return parent::getTags();
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parseWsdl()
     */
    protected function parseWsdl(Wsdl $wsdl)
    {
        foreach ($this->getTags() as $tag) {
            $this->parseHeader($tag);
        }
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parseSchema()
     */
    protected function parseSchema(Wsdl $wsdl, Schema $schema)
    {
        $this->parseWsdl($wsdl);
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\Parser\Wsdl\AbstractParser::parsingTag()
     */
    protected function parsingTag()
    {
        return WsdlDocument::TAG_HEADER;
    }
    /**
     * @param Header $header
     */
    public function parseHeader(Header $header)
    {
        $operation = $header->getParentOperation();
        if ($operation !== null) {
            $serviceMethod = $this->getGenerator()->getServiceMethod($operation->getName());
            if ($serviceMethod !== null) {
                $serviceMethod
                    ->addMeta(self::META_SOAP_HEADERS, array($header->getHeaderRequired()))
                    ->addMeta(self::META_SOAP_HEADER_NAMES, array($header->getHeaderName()))
                    ->addMeta(self::META_SOAP_HEADER_TYPES, array($header->getHeaderType()))
                    ->addMeta(self::META_SOAP_HEADER_NAMESPACES, array($header->getHeaderNamespace()));
            }
        }
    }
}
