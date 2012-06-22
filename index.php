<?php

require 'config.php';
require 'soap.php';
require 'auth.php';
require 'view.php';
require 'controller.php';

$view = new View();
$controller = new Controller($view);
$controller->execute();
$view->render();

?>
