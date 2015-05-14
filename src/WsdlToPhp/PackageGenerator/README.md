WsdlToPhp Package Generator
===========================

Package Generator eases the creation of a PHP package in order to call any SOAP oriented Web Service.

Its purpose is to provide a full OOP approach to send SOAP requests without needing any third party library.

The generated package is a standalone wihtout any dependencies. It's only based on native PHP SoapClient class. After its generation, you can move it anywhere you want and use it right away.

The generated package does not need PEAR nor NuSOAP nor anything else, at least PHP 5.3.3, SoapClient and DOM (which are natively installed from this PHP version)! 

Usage
-----

To generate a package, nothing as simple as this:
```
    $ cd /path/to/WsdlToPhp/PackageGenerator/
    $ composer install
    $ php console wsdltophp:generate:package -h => display help
    $ php console wsdltophp:generate:package \
        --wsdl-urlorpath="http://www.mydomain.com/wsdl.xml" \
        --wsdl-destination="/path/to/where/the/package/must/be/generated/" \
        --wsdl-prefix="MyPackage" \
        --force
    $ cd /path/to/where/the/package/must/be/generated/
    $ ls -la => enjoy!
```
Usage with full options
-----------------------

To generate a package, nothing as simple as this:
```
    $ cd /path/to/WsdlToPhp/PackageGenerator/
    $ composer install
    $ php console wsdltophp:generate:package -h => display help
    $ php console wsdltophp:generate:package \
        --wsdl-urlorpath="http://developer.ebay.com/webservices/latest/ebaySvc.wsdl" \
        --wsdl-proxy-host="****************************" \
        --wsdl-proxy-port=*******  \
        --wsdl-proxy-login="*******" \
        --wsdl-proxy-password="*******" \
        --wsdl-destination='/var/www/Api/' \
        --wsdl-prefix="Api" \
        --wsdl-category="cat" \
        --wsdl-subcategory="" \
        --wsdl-gathermethods="start" \
        --wsdl-reponseasobj=false \
        --wsdl-sendarrayparam=false \
        --wsdl-genautoload=true \
        --wsdl-genwsdlclass=true \
        --wsdl-paramsasarray=false \
        --wsdl-inherits="" \
        --wsdl-genericconstants=false \
        --wsdl-gentutorial=true \
        --wsdl-addcomments="date:2015-04-22" \
        --wsdl-addcomments="author:Me" \
        --wsdl-addcomments="release:1.1.0" \
        --wsdl-addcomments="team:Dream" \
        --force
    $ cd /var/www/Api/
    $ ls -la => enjoy!
```
Tests
-----

You can run the unit tests with the following command:
```
    $ cd /path/to/WsdlToPhp/PackageGenerator/
    $ composer install
    $ phpunit
```
You have several ```testsuite```s available which run test in the proper order:

- configuration: tests configuration readers
- utils: tests utils class
- domhandler: tests dom handlers
- model: tests models
- container : tests containers
- parser: tests parsers

```
    $ cd /path/to/WsdlToPhp/PackageGenerator/
    $ composer install
    $ phpunit --testsuite=model
```