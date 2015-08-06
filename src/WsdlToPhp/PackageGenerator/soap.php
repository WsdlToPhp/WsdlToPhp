<?php
$s=  new \SoapClient('https://webservices.netsuite.com/wsdl/v2015_1_0/netsuite.wsdl');
print_r($s->__getTypes());
