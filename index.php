<?php

require_once 'payutc-json-client/jsonclient/JsonClient.class.php';

require_once 'config.php';
require_once 'soap.php';
require_once 'auth.php';
require_once 'view.php';
require_once 'controller.php';

$view = new View();
$controller = new Controller($view);
$controller->execute();
$controller->set_menu();
$view->render();

?>
