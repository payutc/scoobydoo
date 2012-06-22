<?php

$AADMIN = @new SoapClient($CONF['soap_url']."/AADMIN.class.php?wsdl");

/*
// UNCOMMENT TO DEBUG SOAP
echo "<pre>";
print_r($AADMIN->__getFunctions());

print_r($AADMIN->getArray());
print_r($AADMIN->getArray2());
echo "</pre>";
exit();
// */
