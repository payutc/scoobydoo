<?php

ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');

$AADMIN = @new SoapClient($CONF['soap_url'].'/AADMIN.class.php?wsdl');


// UNCOMMENT TO DEBUG SOAP
/*
echo "<pre>";
print_r($AADMIN->__getFunctions());
<<<<<<< HEAD
exit();
print_r($AADMIN->getArray());
print_r($AADMIN->getArray2());

print_r($AADMIN->get_categories());
=======

print_r($AADMIN->get_categorie(125));
>>>>>>> 4c89098e9b478d8eb277e6d7b62bf9d53192d5aa
print_r($AADMIN->get_articles());
echo "</pre>";
exit();
//  */

