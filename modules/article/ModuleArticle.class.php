<?php

require_once 'modules/Module.class.php';
require_once 'config.php';


class ModuleArticle extends Module {

	public function action_index() {
		global $AADMIN;
		$categories = $AADMIN->get_categories(); $categories = $categories['success'];
		$fundations = $AADMIN->get_fundations_with_right("GESARTICLE"); $fundations=$fundations['success'];
		$article_parents = array();
		foreach($fundations as $fundation) {
			$article_parents[] = array('id'=>'fun'.$fundation['id'], 'name'=>'asso : '.$fundation['name']);
		}
		foreach($categories as $categorie) {
			$article_parents[] = array('id'=>$categorie['id'], 'name'=>'catégorie : '.$categorie['name']);
		}
		$this->view->add_param('categorie_parents', $article_parents);
		$this->view->add_param('categories', $categories);
		$this->view->add_param('fundations', $fundations);
		$modulepath = $this->get_path_module();
		$this->view->set_template('html');
		$this->view->add_jsfile($modulepath.'res/js/jqtree.jquery.js');
		$this->view->add_jsfile($modulepath.'res/js/spin.min.js');
		$this->view->add_jsfile('?module=article&action=mainjs');
		$this->view->add_cssfile($modulepath.'res/css/jqtree.css');
		$this->view->set_view($modulepath.'view/index.phtml');
	}

	public function action_get_tree() {
		global $AADMIN;
		$this->view->set_template('json');


		$fundations = $AADMIN->get_fundations_with_right("GESARTICLE");
		$categories = $AADMIN->get_categories();
		$articles = $AADMIN->get_articles();
		if (!isset($fundations['success']) or !isset($categories['success']) or !isset($articles['success'])) {
			$this->view->set_param(array(array('name'=>'echec')));
			return;
		}
		
		$fundations = $fundations['success'];
		$categories = $categories['success'];
		$articles = $articles['success'];
		
		$arr = array('root' => $this::ArrNode('root','root',NULL,'root'));
		
		foreach ($fundations as $fundation) {
			$arr['fun'.$fundation['id']] = $this::ArrNode(
				$fundation['id'],
				$fundation['name'],
				'root',
				'fundation'
			);
		}

		foreach ($categories as $categorie) {
			if (!$categorie['parent_id']) {
				$parent_id = 'fun'.$categorie['fundation_id'];
			}
			else {
				$parent_id = $categorie['parent_id'];
			}
			$arr[$categorie['id']] = $this::ArrNode(
				$categorie['id'],
				$categorie['name'],
				$parent_id,
				'categorie'
			);
			$arr[$categorie['id']]['fundation_id'] = $categorie['fundation_id'];
		}
		foreach ($articles as $article) {
			$arr[$article['categorie_id']]['children'][] = $this::ArrNode(
				$article['id'],
				$article['name'],
				$article['categorie_id'],
				'article'
			);
		}

		//echo '<pre>';print_r($categories);echo '</pre>';
		
		$tree = $this::generate_tree($arr, 'parent_id');
		$tree = $tree[0]['children'];

		//echo '<pre>';print_r($tree);echo '</pre>';

		//die();

		$this->view->set_param($tree);
	}

	public function action_fundation_details() {
		global $AADMIN;
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
		$result = $AADMIN->get_fundation($id);
		if (isset($result['success'])) {
			$result2 = $AADMIN->get_categories_by_fundation_id($id, true);
			if (isset($result2['success'])) {
				$result['success']['categories'] = $result2['success'];
				$this->view->set_param($result);
			}
			else {
				$this->view->set_param($result2);
			}
		}
		else {
			$this->view->set_param($result);
		}
	}

	public function action_article_details() {
		global $AADMIN;
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
		$result = $AADMIN->get_article($id);
		//echo '<pre>';print_r($article);echo '</pre>'; die();
		// TODO check $result
		$this->view->set_param($result);
	}

	public function action_save_article() {
		global $AADMIN;
		$this->view->set_template('json');
		$name = $_REQUEST['name'];
		$cat_id = $_REQUEST['categorie_id'];
		$price = $_REQUEST['price'];
		$stock = $_REQUEST['stock'];
		$tva = $_REQUEST['tva'];
		$alcool = $_REQUEST['alcool'];
		if (isset($_REQUEST['id']) and !empty($_REQUEST['id'])) {
			$result = $AADMIN->edit_article($_REQUEST['id'], $name, $cat_id, $price, $stock, $tva, $alcool);
		}
		else {
			$result = $AADMIN->add_article($name, $cat_id, $price, $stock, $tva, $alcool);
		}

		$this->view->set_param($result);
	}

	public function action_delete_article() {
		global $AADMIN;
		$this->view->set_template('json');
		$id = $_REQUEST['id'];

		$result = $AADMIN->delete_article($id);

		$this->view->set_param($result);
	}

	public function action_categorie_details() {
		global $AADMIN;
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
		$result = $AADMIN->get_categorie($id);
		//echo '<pre>';print_r($categorie);echo '</pre>'; die();
		// TODO check $result
		$this->view->set_param($result);
	}

	public function action_save_categorie() {
		global $AADMIN;
		$this->view->set_template('json');
		$name = $_REQUEST['name'];
		$parent = $_REQUEST['parent_id'];
		if (substr($parent, 0, 3) == 'fun') {
			$parent_id = NULL;
			$fundation_id = substr($parent, 3);
		}
		else {
			$parent_id = $parent;
			$fundation_id = NULL;
		}
		//print_r(array($_REQUEST['id'], $name, $parent_id, $fundation_id));
		if (isset($_REQUEST['id']) and !empty($_REQUEST['id'])) {
			$result = $AADMIN->edit_categorie($_REQUEST['id'], $name, $parent_id, $fundation_id);
		}
		else {
			$result = $AADMIN->add_categorie($name, $parent_id, $fundation_id);
		}
		
		$this->view->set_param($result);
	}

	public function action_delete_categorie() {
		global $AADMIN;
		$this->view->set_template('json');
		$id = $_REQUEST['id'];

		$result = $AADMIN->delete_categorie($id);

		$this->view->set_param($result);
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
		$this->view->add_param('details_fundation', $url_base.'fundation_details');
		$this->view->add_param('details_article', $url_base.'article_details');
		$this->view->add_param('details_categorie', $url_base.'categorie_details');
		$this->view->add_param('save_article', $url_base.'save_article');
		$this->view->add_param('save_categorie', $url_base.'save_categorie');
		$this->view->add_param('delete_article', $url_base.'delete_article');
		$this->view->add_param('delete_categorie', $url_base.'delete_categorie');
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
					$object['children'] = array();
				}
				$object['children'] = array_merge($object['children'], ModuleArticle::generate_tree($arr, $key_parent, $key));
				$tree[] = $object;
			}
		}
		return $tree;
	}

	public static function ArrNode($id, $name, $parent, $type, $children=array()) {
		return array('id'=>$id, 'name'=>$name, 'parent_id'=>$parent, 'type'=>$type, 'children'=>$children);
	}

	public function get_menus() {
		if($this->has_rights())
			return array("content" => "Articles", "class"=>"", "link"=>$this->get_link_to_action("index")); /*, "submenu"=> array(
							  array("content" => "Gestion", "class"=>"", "link"=>$this->get_link_to_action("index")),
							  array("content" => "", "class"=>"divider", "link"=>"#"),
                              array("content" => "Ajouter un article", "class"=>"", "link"=>$this->get_link_to_action("add-article")), 
                              array("content" => "Ajouter une catégorie", "class"=>"", "link"=>$this->get_link_to_action("add-categorie")))); */
		else
			return;
                              
                              
	}

	public function has_rights() {
		global $AADMIN;
		$right=$AADMIN->get_fundations_with_right("GESARTICLE");
		if(count($right["success"]) > 0)
			return True;
		else
			return False;
	}
}

?>
