<?php
// Pour cette action on veut le template ajax (JSON)
$this->view->set_template("ajax");

$id = $_REQUEST['id'];

$this->view->set_param(array(
	'id'=>$id,
	'name'=>'Une categorie',
));

?>
