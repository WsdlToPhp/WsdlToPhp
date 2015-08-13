<?php

namespace WsdlToPhp\PackageGenerator\Generator;

use WsdlToPhp\PackageGenerator\Model\Schema;
use WsdlToPhp\PackageGenerator\Model\Wsdl;
use WsdlToPhp\PackageGenerator\Model\AbstractModel;
use WsdlToPhp\PackageGenerator\Model\EmptyModel;
use WsdlToPhp\PackageGenerator\Model\Struct;
use WsdlToPhp\PackageGenerator\Model\Service;
use WsdlToPhp\PackageGenerator\Model\Method;
use WsdlToPhp\PackageGenerator\Container\Model\Service as ServiceContainer;
use WsdlToPhp\PackageGenerator\Container\Model\Struct as StructContainer;
use WsdlToPhp\PackageGenerator\ConfigurationReader\GeneratorOptions;
use WsdlToPhp\PackageGenerator\DomHandler\Wsdl\Wsdl as WsdlDocument;

class Generator
{
    /**
     * Wsdl
     * @var Wsdl
     */
    private $wsdl;
    /**
     * @var GeneratorOptions
     */
    private $options;
    /**
     * Used parsers
     * @var GeneratorParsers
     */
    private $parsers;
    /**
     * Used files
     * @var GeneratorFiles
     */
    private $files;
    /**
     * Used containers
     * @var GeneratorContainers
     */
    private $containers;
    /**
     * Used SoapClient
     * @var GeneratorSoapClient
     */
    private $soapClient;
    /**
     * Constructor
     * @param GeneratorOptions $options
     */
    public function __construct(GeneratorOptions $options)
    {
        $this
            ->setOptions($options)
            ->initialize();
    }
    /**
     * @return Generator
     */
    protected function initialize()
    {
        return $this
            ->initContainers()
            ->initParsers()
            ->initFiles()
            ->initWsdl()
            ->initSoapClient()
            ->initDirectory();
    }
    /**
     * @throws \InvalidArgumentException
     * @return Generator
     */
    protected function initSoapClient()
    {
        if (!isset($this->soapClient)) {
            $this->soapClient = new GeneratorSoapClient($this);
        }
        return $this;
    }
    /**
     * @return Generator
     */
    protected function initContainers()
    {
        if (!isset($this->containers)) {
            $this->containers = new GeneratorContainers($this);
        }
        return $this;
    }
    /**
     * @return Generator
     */
    protected function initParsers()
    {
        if (!isset($this->parsers)) {
            $this->parsers = new GeneratorParsers($this);
        }
        return $this;
    }
    /**
     * @return Generator
     */
    protected function initFiles()
    {
        if (!isset($this->files)) {
            $this->files = new GeneratorFiles($this);
        }
        return $this;
    }
    /**
     * @return GeneratorFiles
     */
    public function getFiles()
    {
        return $this->files;
    }
    /**
     * @throws \InvalidArgumentException
     * @return Generator
     */
    protected function initDirectory()
    {
        Utils::createDirectory($this->getOptionDestination());
        if (!is_dir($this->getOptionDestination())) {
            throw new \InvalidArgumentException(sprintf('Unable to use dir "%s" as dir does not exists and its creation has been impossible', $this->getOptionDestination()), __LINE__);
        }
        return $this;
    }
    /**
     * @return Generator
     */
    protected function initWsdl()
    {
        $this->setWsdl(new Wsdl($this, $this->getOptionOrigin(), $this->getUrlContent($this->getOptionOrigin())));
        return $this;
    }
    /**
     * @return Generator
     */
    protected function doSanityChecks()
    {
        $prefix = $this->getOptionPrefix();
        $suffix = $this->getOptionSuffix();
        if (empty($prefix) && empty($suffix)) {
            throw new \InvalidArgumentException('You MUST define at least a prefix or a suffix', __LINE__);
        }
        return $this;
    }
    /**
     * @return Generator
     */
    protected function doParse()
    {
        $this->parsers->doParse();
        return $this;
    }
    /**
     * @return Generator
     */
    protected function doGenerate()
    {
        $this->files->doGenerate();
        return $this;
    }
    /**
     * Generates all classes based on options
     * @return Generator
     */
    public function generateClasses()
    {
        return $this
            ->doSanityChecks()
            ->doParse()
            ->doGenerate();
    }
    /**
     * Gets the struct by its name
     * @uses Generator::getStructs()
     * @param string $structName the original struct name
     * @return Struct|null
     */
    public function getStruct($structName)
    {
        return $this->getStructs()->getStructByName($structName);
    }
    /**
     * Gets a service by its name
     * @param string $serviceName the service name
     * @return Service|null
     */
    public function getService($serviceName)
    {
        return $this->getServices()->getServiceByName($serviceName);
    }
    /**
     * Returns the method
     * @uses Generator::getServiceName()
     * @uses Generator::getService()
     * @uses Service::getMethod()
     * @param string $methodName the original function name
     * @return Method|null
     */
    public function getServiceMethod($methodName)
    {
        return $this->getService($this->getServiceName($methodName)) instanceof Service ? $this->getService($this->getServiceName($methodName))->getMethod($methodName) : null;
    }
    /**
     * @return ServiceContainer
     */
    public function getServices()
    {
        return $this->containers->getServices();
    }
    /**
     * @return StructContainer
     */
    public function getStructs()
    {
        return $this->containers->getStructs();
    }
    /**
     * Sets the optionCategory value
     * @return string
     */
    public function getOptionCategory()
    {
        return $this->options->getCategory();
    }
    /**
     * Sets the optionCategory value
     * @param string $category
     * @return Generator
     */
    public function setOptionCategory($category)
    {
        $this->options->setCategory($category);
        return $this;
    }
    /**
     * Sets the optionGatherMethods value
     * @return string
     */
    public function getOptionGatherMethods()
    {
        return $this->options->getGatherMethods();
    }
    /**
     * Sets the optionGatherMethods value
     * @param string $gatherMethods
     * @return Generator
     */
    public function setOptionGatherMethods($gatherMethods)
    {
        $this->options->setGatherMethods($gatherMethods);
        return $this;
    }
    /**
     * Gets the optionGenericConstantsNames value
     * @return bool
     */
    public function getOptionGenericConstantsNames()
    {
        return $this->options->getGenericConstantsName();
    }
    /**
     * Sets the optionGenericConstantsNames value
     * @param bool $genericConstantsNames
     * @return Generator
     */
    public function setOptionGenericConstantsNames($genericConstantsNames)
    {
        $this->options->setGenericConstantsName($genericConstantsNames);
        return $this;
    }
    /**
     * Gets the optionGenerateTutorialFile value
     * @return bool
     */
    public function getOptionGenerateTutorialFile()
    {
        return $this->options->getGenerateTutorialFile();
    }
    /**
     * Sets the optionGenerateTutorialFile value
     * @param bool $generateTutorialFile
     * @return Generator
     */
    public function setOptionGenerateTutorialFile($generateTutorialFile)
    {
        $this->options->setGenerateTutorialFile($generateTutorialFile);
        return $this;
    }
    /**
     * Gets the optionNamespacePrefix value
     * @return string
     */
    public function getOptionNamespacePrefix()
    {
        return $this->options->getNamespace();
    }
    /**
     * Sets the optionGenerateTutorialFile value
     * @param string $namespace
     * @return Generator
     */
    public function setOptionNamespacePrefix($namespace)
    {
        $this->options->setNamespace($namespace);
        return $this;
    }
    /**
     * Gets the optionAddComments value
     * @return array
     */
    public function getOptionAddComments()
    {
        return $this->options->getAddComments();
    }
    /**
     * Sets the optionAddComments value
     * @param array $addComments
     * @return Generator
     */
    public function setOptionAddComments($addComments)
    {
        $this->options->setAddComments($addComments);
        return $this;
    }
    /**
     * Gets the optionStandalone value
     * @return bool
     */
    public function getOptionStandalone()
    {
        return $this->options->getStandalone();
    }
    /**
     * Sets the optionStandalone value
     * @param bool $standalone
     * @return Generator
     */
    public function setOptionStandalone($standalone)
    {
        $this->options->setStandalone($standalone);
        return $this;
    }
    /**
     * Gets the optionStructClass value
     * @return string
     */
    public function getOptionStructClass()
    {
        return $this->options->getStructClass();
    }
    /**
     * Sets the optionStructClass value
     * @param string $structClass
     * @return Generator
     */
    public function setOptionStructClass($structClass)
    {
        $this->options->setStructClass($structClass);
        return $this;
    }
    /**
     * Gets the optionStructArrayClass value
     * @return string
     */
    public function getOptionStructArrayClass()
    {
        return $this->options->getStructArrayClass();
    }
    /**
     * Sets the optionStructArrayClass value
     * @param string $structArrayClass
     * @return Generator
     */
    public function setOptionStructArrayClass($structArrayClass)
    {
        $this->options->setStructArrayClass($structArrayClass);
        return $this;
    }
    /**
     * Gets the optionSoapClientClass value
     * @return string
     */
    public function getOptionSoapClientClass()
    {
        return $this->options->getSoapClientClass();
    }
    /**
     * Sets the optionSoapClientClass value
     * @param string $soapClientClass
     * @return Generator
     */
    public function setOptionSoapClientClass($soapClientClass)
    {
        $this->options->setSoapClientClass($soapClientClass);
        return $this;
    }
    /**
     * Gets the package name prefix
     * @param bool $ucFirst ucfirst package name prefix or not
     * @return string
     */
    public function getOptionPrefix($ucFirst = true)
    {
        return $ucFirst ? ucfirst($this->getOptions()->getPrefix()) : $this->getOptions()->getPrefix();
    }
    /**
     * Sets the package name prefix
     * @param string $optionPrefix
     * @return Generator
     */
    public function setOptionPrefix($optionPrefix)
    {
        $this->options->setPrefix($optionPrefix);
        return $this;
    }
    /**
     * Gets the package name suffix
     * @param bool $ucFirst ucfirst package name suffix or not
     * @return string
     */
    public function getOptionSuffix($ucFirst = true)
    {
        return $ucFirst ? ucfirst($this->getOptions()->getSuffix()) : $this->getOptions()->getSuffix();
    }
    /**
     * Sets the package name suffix
     * @param string $optionSuffix
     * @return Generator
     */
    public function setOptionSuffix($optionSuffix)
    {
        $this->options->setSuffix($optionSuffix);
        return $this;
    }
    /**
     * Gets the optionBasicLogin value
     * @return string
     */
    public function getOptionBasicLogin()
    {
        return $this->options->getBasicLogin();
    }
    /**
     * Sets the optionBasicLogin value
     * @param string $optionBasicLogin
     * @return Generator
     */
    public function setOptionBasicLogin($optionBasicLogin)
    {
        $this->options->setBasicLogin($optionBasicLogin);
        return $this;
    }
    /**
     * Gets the optionBasicPassword value
     * @return string
     */
    public function getOptionBasicPassword()
    {
        return $this->options->getBasicPassword();
    }
    /**
     * Sets the optionBasicPassword value
     * @param string $optionBasicPassword
     * @return Generator
     */
    public function setOptionBasicPassword($optionBasicPassword)
    {
        $this->options->setBasicPassword($optionBasicPassword);
        return $this;
    }
    /**
     * Gets the optionProxyHost value
     * @return string
     */
    public function getOptionProxyHost()
    {
        return $this->options->getProxyHost();
    }
    /**
     * Sets the optionProxyHost value
     * @param string $optionProxyHost
     * @return Generator
     */
    public function setOptionProxyHost($optionProxyHost)
    {
        $this->options->setProxyHost($optionProxyHost);
        return $this;
    }
    /**
     * Gets the optionProxyPort value
     * @return string
     */
    public function getOptionProxyPort()
    {
        return $this->options->getProxyPort();
    }
    /**
     * Sets the optionProxyPort value
     * @param string $optionProxyPort
     * @return Generator
     */
    public function setOptionProxyPort($optionProxyPort)
    {
        $this->options->setProxyPort($optionProxyPort);
        return $this;
    }
    /**
     * Gets the optionProxyLogin value
     * @return string
     */
    public function getOptionProxyLogin()
    {
        return $this->options->getProxyLogin();
    }
    /**
     * Sets the optionProxyLogin value
     * @param string $optionProxyLogin
     * @return Generator
     */
    public function setOptionProxyLogin($optionProxyLogin)
    {
        $this->options->setProxyLogin($optionProxyLogin);
        return $this;
    }
    /**
     * Gets the optionProxyPassword value
     * @return string
     */
    public function getOptionProxyPassword()
    {
        return $this->options->getProxyPassword();
    }
    /**
     * Sets the optionProxyPassword value
     * @param string $optionProxyPassword
     * @return Generator
     */
    public function setOptionProxyPassword($optionProxyPassword)
    {
        $this->options->setProxyPassword($optionProxyPassword);
        return $this;
    }
    /**
     * Gets the optionOrigin value
     * @return string
     */
    public function getOptionOrigin()
    {
        return $this->options->getOrigin();
    }
    /**
     * Sets the optionOrigin value
     * @param string $optionOrigin
     * @return Generator
     */
    public function setOptionOrigin($optionOrigin)
    {
        $this->options->setOrigin($optionOrigin);
        $this->initWsdl();
        return $this;
    }
    /**
     * Gets the optionDestination value
     * @return string
     */
    public function getOptionDestination()
    {
        return realpath($this->options->getDestination()) . DIRECTORY_SEPARATOR;
    }
    /**
     * Sets the optionDestination value
     * @param string $optionDestination
     * @return Generator
     */
    public function setOptionDestination($optionDestination)
    {
        $this->options->setDestination(realpath($optionDestination) . DIRECTORY_SEPARATOR);
        $this->initDirectory();
        return $this;
    }
    /**
     * Gets the optionSoapOptions value
     * @return string
     */
    public function getOptionSoapOptions()
    {
        return $this->options->getSoapOptions();
    }
    /**
     * Sets the optionSoapOptions value
     * @param array $optionSoapOptions
     * @return Generator
     */
    public function setOptionSoapOptions($optionSoapOptions)
    {
        $this->options->setSoapOptions($optionSoapOptions);
        return $this;
    }
    /**
     * Gets the WSDL
     * @return Wsdl|null
     */
    public function getWsdl()
    {
        return $this->wsdl;
    }
    /**
     * Sets the WSDLs
     * @param Wsdl $wsdl
     * @return Generator
     */
    protected function setWsdl(Wsdl $wsdl)
    {
        $this->wsdl = $wsdl;
        return $this;
    }
    /**
     * Adds Wsdl location
     * @param Wsdl $wsdl
     * @param string $schemaLocation
     * @return Generator
     */
    public function addSchemaToWsdl(Wsdl $wsdl, $schemaLocation)
    {
        if (!empty($schemaLocation) && $wsdl->getContent() instanceof WsdlDocument && $wsdl->getContent()->getExternalSchema($schemaLocation) === null) {
            $wsdl->getContent()->addExternalSchema(new Schema($wsdl->getGenerator(), $schemaLocation, $this->getUrlContent($schemaLocation)));
        }
        return $this;
    }
    /**
     * Gets gather name class
     * @param AbstractModel $model the model for which we generate the folder
     * @return string
     */
    private function getGather(AbstractModel $model)
    {
        return Utils::getPart($this->getOptionGatherMethods(), $model->getCleanName());
    }
    /**
     * Returns the service name associated to the method/operation name in order to gather them in one service class
     * @uses Generator::getGather()
     * @param string $methodName original operation/method name
     * @return string
     */
    public function getServiceName($methodName)
    {
        return ucfirst($this->getOptionGatherMethods() === GeneratorOptions::VALUE_NONE ? Service::DEFAULT_SERVICE_CLASS_NAME : $this->getGather(new EmptyModel($this, $methodName)));
    }
    /**
     * @param GeneratorOptions $options
     * @return Generator
     */
    protected function setOptions(GeneratorOptions $options = null)
    {
        $this->options = $options;
        return $this;
    }
    /**
     * @return GeneratorOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
    /**
     * @param GeneratorSoapClient $soapClient
     * @return Generator
     */
    protected function setSoapClient(GeneratorSoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
        return $this;
    }
    /**
     * @return GeneratorSoapClient
     */
    public function getSoapClient()
    {
        return $this->soapClient;
    }
    /**
     * @param string $url
     * @return string
     */
    public function getUrlContent($url)
    {
        if (strpos($url, '://') !== false) {
            return Utils::getContentFromUrl($url, $this->getOptionBasicLogin(), $this->getOptionBasicPassword(), $this->getOptionProxyHost(), $this->getOptionProxyPort(), $this->getOptionProxyLogin(), $this->getOptionProxyPassword());
        } elseif (is_file($url)) {
            return file_get_contents($url);
        }
        return null;
    }
}
