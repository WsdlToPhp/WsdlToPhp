<?php

namespace WsdlToPhp\PackageGenerator\DomHandler\Wsdl;

use WsdlToPhp\PackageGenerator\Model\Schema as Model;
use WsdlToPhp\PackageGenerator\Container\Model\Schema as ModelContainer;

class Wsdl extends AbstractDocument
{
    /**
     * @see \WsdlToPhp\PackageGenerator\DomHandler\AbstractDomDocumentHandler::__construct()
     * @param \DOMDocument $domDocument
     * @return Wsdl
     */
    public function __construct(\DOMDocument $domDocument)
    {
        parent::__construct($domDocument);
        $this->externalSchemas = new ModelContainer();
    }
    /**
     * @var ModelContainer
     */
    protected $externalSchemas;
    /**
     * @param Model $schema
     * @return \WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl
     */
    public function addExternalSchema(Model $schema)
    {
        $this->externalSchemas->add($schema);
        return $this;
    }
    /**
     * @param string $name
     * @return \WsdlToPhp\PackageGenerator\Model\Schema|null
     */
    public function getExternalSchema($name)
    {
        return $this->externalSchemas->getSchemaByName($name);
    }
    /**
     * @return \WsdlToPhp\PackageGenerator\Container\Model\Schema
     */
    public function getExternalSchemas()
    {
        return $this->externalSchemas;
    }
}
