<?php
require "config.php";
require "soap.php";
require "auth.php";
require "view.php";
require "controller.php";

$View = new View();
$Controller = new Controller($View);
$Controller->execute();
$View->render();

?>
