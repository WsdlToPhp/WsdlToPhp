WsdlToPhp Package Generator
===========================

Package Generator eases the creation of a PHP package in order to call any SOAP oriented Web Service.
Its purpose is to provide a full OOP approach to send SOAP requests without needing any third party library.
The generated package is a standalone. It's only based on native PHP SoapClient class. After its generation, you can move it anywhere you want.
It does not need PEAR nor NuSOAP nor anything else, at least PHP 5.3.3, SoapClient and DOM (which are natively installed from this PHP version)! 

Usage
-----
To generate a package, nothing as simple as this:

    $ cd path/to/WsdlToPhp/PackageGenerator/
    $ composer install
    $ php console wsdltophp:generate:package -h => display help
    $ php console wsdltophp:generate:package \
        --wsdl-urlorpath="http://www.mydomain.com/wsdl.xml" \
        --wsdl-destination="/path/to/where/the/package/must/be/generated/" \
        --wsdl-prefix="MyPackage" \
        --force
    $ cd /path/to/where/the/package/must/be/generated/
    $ ls -la => enjoy!

Tests
-----

You can run the unit tests with the following command:

    $ cd path/to/WsdlToPhp/PackageGenerator/
    $ composer install
    $ phpunit