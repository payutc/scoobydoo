<?
// Pour cette action on veut le template ajax (JSON)
$this->view->set_template("ajax");

class Node {
	public $id, $label, $children;
	public function __construct($id, $label, $children=array()) {
		$this->id = $id;
		$this->label = $label;
		$this->children = $children;
	}
}

$this->view->set_param(array(
	new Node(1, 'node1', array(
		new Node(2, 'child1', array(
			new Node(6, 'child4'),
			new Node(7, 'child5'),
		)),
		new Node(3, 'child2'),
	)),
	new Node(4, 'node2', array(
		new Node(5, 'child3')
	))
));

?>
