<?

require 'modules/Module.class.php';

class Node {
	public $id, $label, $children;
	public function __construct($id, $label, $children=array()) {
		$this->id = $id;
		$this->label = $label;
		$this->children = $children;
	}
}

class ModuleArticle extends Module {

	public function action_index(&$view) {
		$modulepath = $this->get_path_module();
		$view->set_template('html');
		$view->add_jsfile('libs/jquery-1.7.2.min.js');
		$view->add_jsfile($modulepath.'res/js/jqtree.jquery.js');
		$view->add_jsfile($modulepath.'?module=article&action=mainjs');
		$view->set_view($modulepath.'view/index.phtml');
		return $view;
	}

	public function action_get_tree(&$view) {
		$view->set_template('json');
		$view->set_param(array(
			new Node(1, 'node1', array(
				new Node(2, 'child1'),
				new Node(3, 'child2'),
			)),
			new Node(4, 'node2', array(
				new Node(5, 'child3')
			))
		));
	}

	public function action_mainjs(&$view) {
		// Pour cette action on veut le template JS
		$this->view->set_template("js");

		// On veut égallement une vue particuliére
		$myview = $this->view_to_filename("main.js");
		$this->view->set_view($myview);

		// Configuration des parametres nécessaires à la vue (les urls ajax)
		global $CONF;
		$url_base = $CONF["scoobydoo_url"]."?module=article&action=";
		$this->view->add_param("get_tree", $url_base."get_tree");
		$this->view->add_param("details_article", $url_base."details_article");
		$this->view->add_param("details_categorie", $url_base."details_categorie");
		$this->view->add_param("save_article", $url_base."save_article");
		$this->view->add_param("save_categorie", $url_base."save_categorie");
	}
}

?>
