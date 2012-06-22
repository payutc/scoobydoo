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
		global $AADMIN;
		$this->view->set_template('json');
		$arr = array(
			array('id'=> 0, 'label'=>'label0', 'parent'=>NULL),
			array('id'=> 1, 'label'=>'label1', 'parent'=>0),
			array('id'=> 2, 'label'=>'label2', 'parent'=>0),
			array('id'=> 3, 'label'=>'label3', 'parent'=>1),
			array('id'=> 4, 'label'=>'label4', 'parent'=>1),
		);
		$temp = $AADMIN->get_categories();
		$categories = array('root' => array('id'=>'root', 'label'=>'root', 'parent_id'=>null, 'children'=>array()));
		foreach ($temp as $categorie) {
			$categorie['children'] = array();
			if (!$categorie['parent_id']) {
				$categorie['parent_id'] = 'root';
			}
			$categories[$categorie['id']] = $categorie;
		}
		$articles = $AADMIN->get_articles();
		foreach ($articles as $article) {
			$categories[$article['parent_id']]['children'][] = $article;
		}

		//echo '<pre>';print_r($categories);echo '</pre>';
		
		$tree = $this::generate_tree($categories, 'parent_id');

		//echo '<pre>';print_r($tree);echo '</pre>';

		//die();

		$this->view->set_param($tree);
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

	
	/**
	 * A partir d'une array cré l'arbre hierarchisé associé, fonction récursive.
	 *
	 * @param $arr (array) contient la liste des éléments à hierarchisé,
	 * 						ces éléments sont sous la forme clef=>array(champs1=>v1, champs2=>v2...)
	 * @param $key_parent (string) le nom du champs qui contient l'id du parent
	 * @param $parent le parent à utiliser comme base de l'arbre
	 */
	public static function generate_tree(array $arr, $key_parent, $parent = NULL) {
		$tree = array();
		foreach ($arr as $key=>&$object) {
			if ($object[$key_parent] == $parent) {
				unset($arr[$key]);
				if (!$object['children']) {
					$object['children'] = ModuleArticle::generate_tree($arr, $key_parent, $key);
				}
				$tree[] = $object;
			}
		}
		return $tree;
	}
}

?>
