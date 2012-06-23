<?php

ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');

$AADMIN = @new SoapClient($CONF['soap_url'].'/AADMIN.class.php?wsdl');


// UNCOMMENT TO DEBUG SOAP
/*
echo "<pre>";
print_r($AADMIN->__getFunctions());


print_r($AADMIN->get_fundations());

//print_r($AADMIN->get_categories());

print "get_categorie 582\n";
print_r($AADMIN->get_categorie(582));
//print_r($AADMIN->edit_categorie(125, "TEST", 1));
//print_r($AADMIN->edit_categorie(582, "BOISSONS", NULL));

print "get_article 591\n";
print_r($AADMIN->get_article(591));
//print_r($AADMIN->edit_article(591, "Pampryl ananas", 586, 80, 102));
print_r($AADMIN->get_article(591));

//print_r($AADMIN->get_articles());
echo "</pre>";
exit();
//  */

