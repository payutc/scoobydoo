<?php

$AADMIN = @new SoapClient($CONF['soap_url'].'/AADMIN.class.php?wsdl');


// UNCOMMENT TO DEBUG SOAP
/*
echo "<pre>";
print_r($AADMIN->__getFunctions());

print_r($AADMIN->get_categorie(125));
print_r($AADMIN->get_articles());
echo "</pre>";
exit();
//  */

