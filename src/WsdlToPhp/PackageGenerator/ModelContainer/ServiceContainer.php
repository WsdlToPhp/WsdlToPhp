<?php

namespace WsdlToPhp\PackageGenerator\ModelContainer;

use WsdlToPhp\PackageGenerator\Model\Service;

class ServiceContainer extends ModelContainer
{
    protected function modelClass()
    {
        return 'WsdlToPhp\\PackageGenerator\\Model\\Service';
    }
    /**
     * Adds a service
     * @param string $serviceName the service name to which add the method
     * @param string $methodName the original function name
     * @param string $methodParameter the original parameter name
     * @param string $methodReturn the original return name
     * @return ServiceContainer
     */
    private function addService($serviceName, $methodName, $methodParameter, $methodReturn)
    {
        if ($this->get($serviceName) === null) {
            $this->add(new Service($serviceName));
        }
        $serviceMethod = $this->get($serviceName)->getMethod($methodName);
        /**
         * Service method does not already exist, register it
         */
        if ($serviceMethod === null) {
            $this->get($serviceName)->addMethod($methodName, $methodParameter, $methodReturn);
        } elseif ($serviceMethod->getParameterType() != $methodParameter) {
            /**
             * Service method exists with a different signature, register it too by identifying the service functions as non unique functions
             */
            $serviceMethod->setIsUnique(false);
            $this->get($serviceName)->addMethod($methodName, $methodParameter, $methodReturn, false);
        }
    }
    /**
     * @see \WsdlToPhp\PackageGenerator\ModelContainer\AbstractModelContainer::get()
     * @return Service
     */
    public function get($value, $key = parent::KEY_NAME)
    {
        return parent::get($value, $key);
    }
}
