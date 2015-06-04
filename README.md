# WsdlToPhp
This project aims to provide multiple components that eases the use of SOAP oriented Web Services.

To do this, the first component if the Package Generator.

## The package generator
This component as it is named provides an easy way to generate a PHP package.

The generated package then provides all the methods to send any request to any operation provided by the Web Service.

You can find this component under ```src/WsdlToPhp/PackageGenerator``` directory.

## The php generator
This component as it is named provides an easy way to generate a PHP source code.

This component is used by the package generator to ensure stability and consistency among generated PHP files.

You can find this component under ```src/WsdlToPhp/PhpGenerator``` directory.

## The bundle generator
The bundle generator will aim to provide an easy way to generate a symfony bundle based on the generated package.

The generated bundle then provides services and other things to use the generated package as a symfony bundle.

This component will be available under ```src/WsdlToPhp/BundleGenerator``` directory.

## Roadmap
### First step: finalize package generator refactoring
As you must know, the package generator is based on the original project WsdlToPhp with a more robust and extensible way.

Nevertheless, it still remains some code parts that are not optimal and require refactoring.

This step will be developed under the ```feature/mandevilla``` branch.

### Second step: optimize what can be optimized
In order to be able to provide a real interesting bundle generation experience, it's essential to be sure that the package generator is as fast as possible.

This step will be developed under the ```feature/anthurium``` branch.

### Third step: create the bundle generator
As soon as the package generator is really finished, this generator will be developped under the ```feature/orchid```.