<?
// Pour cette action on veut le template ajax (JSON)
$this->view->set_template("ajax");

//$name = $_REQUEST['name'];

if (isset($_REQUEST['id'])) {
	// EDITION d'un article déjà existant
}
else {
	// AJOUT d'un nouvel article
}

$this->view->set_param(array('success'=> 'ok'));
?>

