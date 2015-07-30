<?php

namespace Api\ServiceType;

use \WsdlToPhp\PackageBase\AbstractSoapClientBase;

/**
 * This class stands for Login ServiceType
 * @package Api
 * @subpackage Services
 * @release 1.1.0
 */
class ApiLogin extends AbstractSoapClientBase
{
    /**
     * Method to call the operation originally named Login
     * Meta informations extracted from the WSDL
     * - documentation: Выполняет авторизацию внешней системы и открывает сеанс работы
     * @uses AbstractSoapClientBase::getSoapClient()
     * @uses AbstractSoapClientBase::setResult()
     * @uses AbstractSoapClientBase::getResult()
     * @uses AbstractSoapClientBase::saveLastError()
     * @param string $login
     * @param string $password
     * @return string|bool
     */
    public function Login($login, $password)
    {
        try {
            $this->setResult(self::getSoapClient()->Login($login, $password));
            return $this->getResult();
        } catch (\SoapFault $soapFault) {
            $this->saveLastError(__METHOD__, $soapFault);
            return false;
        }
    }
    /**
     * Returns the result
     * @see AbstractSoapClientBase::getResult()
     * @return string
     */
    public function getResult()
    {
        return parent::getResult();
    }
    /**
     * Method returning the class name
     * @return string __CLASS__
     */
    public function __toString()
    {
        return __CLASS__;
    }
}
