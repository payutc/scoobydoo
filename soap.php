<?php

ini_set('soap.wsdl_cache_enabled', '0'); 
ini_set('soap.wsdl_cache_ttl', '0');

$AADMIN = @new SoapClient($CONF['soap_url'].'AADMIN.class.php?wsdl');


