<?

require 'modules/Module.class.php';
require 'config.php';

class Node {
	public $id, $label, $children;
	public function __construct($id, $label, $children=array()) {
		$this->id = $id;
		$this->label = $label;
		$this->children = $children;
	}
}

class ModuleArticle extends Module {

	public function action_index() {
		$modulepath = $this->get_path_module();
		$this->view->set_template('html');
		$this->view->add_jsfile('libs/jquery-1.7.2.min.js');
		$this->view->add_jsfile($modulepath.'res/js/jqtree.jquery.js');
		$this->view->add_jsfile('?module=article&action=mainjs');
		$this->view->set_view($modulepath.'view/index.phtml');
	}

	public function action_get_tree() {
		$this->view->set_template('json');
		$this->view->set_param(array(
			new Node(1, 'node1', array(
				new Node(2, 'child1'),
				new Node(3, 'child2'),
			)),
			new Node(4, 'node2', array(
				new Node(5, 'child3')
			))
		));
	}

	public function action_article_details() {
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
		$this->view->set_param(array(
			'id'=>$id,
			'name'=>'coca',
			'price'=>100,
		));
	}

	public function action_save_article() {
		$this->view->set_template('json');
		if (isset($_REQUEST['id'])) {
			// EDITION d'un article déjà existant
		}
		else {
			// AJOUT d'un nouvel article
		}

		$this->view->set_param(array('success'=> 'ok'));
	}

	public function action_categorie_details() {
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
		$this->view->set_param(array(
			'id'=>$id,
			'name'=>'Une categorie',
		));
	}

	public function action_save_categorie() {
		$this->view->set_template('json');
		if (isset($_REQUEST['id'])) {
			// EDITION d'un article déjà existant
		}
		else {
			// AJOUT d'un nouvel article
		}

		$this->view->set_param(array('success'=> 'ok'));
	}


	public function action_mainjs() {
		// Pour cette action on veut le template JS
		$this->view->set_template('js');

		// On veut égallement une vue particuliére
		$myview = $this->get_path_module().'view/main.js';
		$this->view->set_view($myview);

		// Configuration des parametres nécessaires à la vue (les urls ajax)
		global $CONF;
		$url_base = $CONF['scoobydoo_url'].'?module=article&action=';
		$this->view->add_param('get_tree', $url_base.'get_tree');
		$this->view->add_param('details_article', $url_base.'article_details');
		$this->view->add_param('details_categorie', $url_base.'categorie_details');
		$this->view->add_param('save_article', $url_base.'save_article');
		$this->view->add_param('save_categorie', $url_base.'save_categorie');
	}
}

?>
