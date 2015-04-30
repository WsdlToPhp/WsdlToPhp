The Wsdl parsers
================

The key of theses parsers is to do the minimum treatments meaning that we must load the minimum amount of nodes in order to speed it up and to low the memory usage.

Neverthless, the goals of these parsers are various:

 - Get the maximum amount of informations about each structs and operations
 - Consolidate the informations parsed by the SoapClient parsers they can't see as:
   - SoapHeaders (header tags)
   - Enumerations values
   - Restrictions on parameters
   - Default parameter value
   - Abstract elements
   - Input/Output parameters type
   - Values of type array
   - Inheritance between elements

Knowing this, it is simpler to understand why complexType, simpleType, element are not parsed as parsing them would mean that:

 - We would retrieve each tag
 - For each tag we would apply various methods to test the presence of each possible information we want to get (possibly none)
 
This show that potentially we would load lots of nodes for nothing if they don't contain anything.
 
Otherwise, if we load all the annotation nodes that contains appinfo or documentation nodes for example, if annotations are numerous, it's good meaning that the Web Service is well documented.
On the other hand, if there is no annotation node, then we won't do anything meaning that we won't loose time to parse any node.