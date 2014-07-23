<?php

require_once 'modules/Module.class.php';
require_once 'config.php';


class ModuleArticle extends Module {

    protected $service = "GESARTICLE";

    public function action_index() {
        // Template conf
        $this->view->set_template('html');
        $modulepath = $this->get_path_module();
        $this->view->set_view($modulepath.'view/index.phtml');
        $this->view->add_jsfile($modulepath.'res/js/tree.jquery.js');
        $this->view->add_jsfile($modulepath.'res/js/spin.min.js');
        $this->view->add_jsfile('?module='.$this->get_module_name().'&action=mainjs');
        $this->view->add_cssfile($modulepath.'res/css/jqtree.css');

        // Get informations
        $fundations = $this->json_client->getFundations();
        $categories = $this->json_client->getCategories();

        $article_parents = array();
        foreach($fundations as $fundation) {
            if($fundation->fun_id)
	            $article_parents[] = array('id'=>'fun'.$fundation->fun_id, 'name'=>'asso : '.$fundation->name);
        }

        foreach($categories as $categorie) {
	        $article_parents[] = array('id'=>$categorie->id, 'name'=>'catégorie : '.$categorie->name);
        }
        $this->view->add_param('categorie_parents', $article_parents);
        $this->view->add_param('categories', $categories);
        $this->view->add_param('fundations', $fundations);
    }

	public function action_get_tree() {
		$this->view->set_template('json');

        try {
		    $fundations = $this->json_client->getFundations();
		    $categories = $this->json_client->getCategories();
		    $articles = $this->json_client->getProducts();
        } catch (Exception $e) {
			$this->view->set_param(array(array('name'=>'echec')));
            return;
        }

		$arr = array('root' => $this::ArrNode('root','root',NULL,'root',NULL));
		
		foreach ($fundations as $fundation) {
            if($fundation->fun_id) {
			    $arr['fun'.$fundation->fun_id] = $this::ArrNode(
				    'fun'.$fundation->fun_id,
				    $fundation->name,
				    'root',
				    'fundation',
                    $fundation->fun_id
			    );
            }
		}

		foreach ($categories as $categorie) {
			if (!$categorie->parent_id) {
				$parent_id = 'fun'.$categorie->fundation_id;
			}
			else {
				$parent_id = $categorie->parent_id;
			}
			$arr[$categorie->id] = $this::ArrNode(
				$categorie->id,
				$categorie->name,
				$parent_id,
				'categorie',
                $categorie->fundation_id
			);
			$arr[$categorie->id]['fundation_id'] = $categorie->fundation_id;
		}
		foreach ($articles as $article) {
			$arr[$article->categorie_id]['children'][] = $this::ArrNode(
				$article->id,
				$article->name,
				$article->categorie_id,
				'article',
                $article->fundation_id
			);
		}

		// echo '<pre>';print_r($categories);echo '</pre>';
		
		$tree = $this::generate_tree($arr, 'parent_id');
		$tree = $tree[0]['children'];

		// echo '<pre>';print_r($tree);echo '</pre>';
		// die();

		$this->view->set_param($tree);
	}

	public function action_fundation_details() {
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
        $result['success']['categories'] = $this->json_client->getCategories(array("fun_ids" => json_encode(array($id))));	
		$this->view->set_param($result);
	}

	public function action_article_details() {
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
        $fun_id = $_REQUEST['fun_id'];
		$result = $this->json_client->getProduct(array("obj_id" => $id, "fun_id" => $fun_id));
        $result->success->image_url = $this->get_link_to_action('get_image') . "&image_id=".$result->success->image;
        // TODO check $result
		$this->view->set_param($result);
	}

	public function action_save_article() {
		$this->view->set_template('json');
		$name = $_REQUEST['name'];
		$cat_id = $_REQUEST['categorie_id'];
		$fun_id = $_REQUEST['fundation_id'];
		$stock = $_REQUEST['stock'];
		$alcool = isset($_REQUEST['alcool']) ? 1 : 0;
		$cotisant = isset($_REQUEST['cotisant']) ? 1 : 0;
  	
        $price = str_replace(',','.', $_REQUEST['price']);
        $price *= 100;

        $tva = str_replace(',','.', $_REQUEST['tva']);

        $imageId = 0;
        if(!empty($_FILES['image']) && $_FILES["image"]["error"] == 0 && $_FILES["image"]["size"] < 1*1024*1024){
            $image = base64_encode(file_get_contents($_FILES["image"]["tmp_name"]));

            if(!empty($image)){
                $imageId = $this->json_client->uploadImage(array("image" => $image));
            }      
        }
    
        if(!empty($_REQUEST['delete_image'])){
            $imageId = -1;
        }

        $product = array(
            "obj_id" => null, // null implique la création d'un article
            "name" => $name,
            "parent" => $cat_id, 
            "prix" => $price, 
            "stock" => $stock, 
            "alcool" => $alcool, 
            "image" => $imageId, 
            "fun_id" => $fun_id,
            "tva" => $tva,
            "cotisant" => $cotisant
        );


		if (isset($_REQUEST['id']) and !empty($_REQUEST['id'])) {
            $product['obj_id'] = $_REQUEST['id'];
		}
        $result = $this->json_client->setProduct($product);
		$this->view->set_param($result);
	}

	public function action_delete_article() {
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
        $fun_id = $_REQUEST['fundation_id'];

		$result = $this->json_client->deleteProduct(array("obj_id" => $id, "fun_id" => $fun_id));
		$this->view->set_param($result);
	}

	public function action_categorie_details() {
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
        $fun_id = $_REQUEST['fun_id'];

		$result = $this->json_client->getCategory(array("obj_id" => $id, "fun_id" => $fun_id));
		//echo '<pre>';print_r($categorie);echo '</pre>'; die();
		// TODO check $result
		$this->view->set_param($result);
	}

	public function action_save_categorie() {
		$this->view->set_template('json');
		$name = $_REQUEST['name'];
		$parent = $_REQUEST['parent_id'];
        $fundation_id = $_REQUEST['fundation_id'];
		if (substr($parent, 0, 3) == 'fun') {
			$parent_id = NULL;
		}
		else {
			$parent_id = $parent;
		}
        
        $category = array(
            "obj_id" => null, // null implique la creation d'une nouvelle category
            "name" => $name,
            "parent_id" => $parent_id,
            "fun_id" => $fundation_id
        );

		//print_r(array($_REQUEST['id'], $name, $parent_id, $fundation_id));
		if (isset($_REQUEST['id']) and !empty($_REQUEST['id'])) {
			$category['obj_id'] = $_REQUEST['id'];
		}
		
        $result = $this->json_client->setCategory($category);
		$this->view->set_param($result);
	}

	public function action_delete_categorie() {
		$this->view->set_template('json');
		$id = $_REQUEST['id'];
        $fun_id = $_REQUEST['fundation_id'];

		$result = $this->json_client->deleteCategory(array("obj_id" => $id, "fun_id" => $fun_id));

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
		$url_base = $CONF['scoobydoo_url'];
		$this->view->add_param('get_tree', $url_base.$this->get_link_to_action('get_tree'));
		$this->view->add_param('details_fundation', $url_base.$this->get_link_to_action('fundation_details'));
		$this->view->add_param('details_article', $url_base.$this->get_link_to_action('article_details'));
		$this->view->add_param('details_categorie', $url_base.$this->get_link_to_action('categorie_details'));
		$this->view->add_param('save_article', $url_base.$this->get_link_to_action('save_article'));
		$this->view->add_param('save_categorie', $url_base.$this->get_link_to_action('save_categorie'));
		$this->view->add_param('delete_article', $url_base.$this->get_link_to_action('delete_article'));
		$this->view->add_param('delete_categorie', $url_base.$this->get_link_to_action('delete_categorie'));
	}

    public function action_get_image() {
        $image_id = $_REQUEST['image_id'];
        $img_base64 = $this->json_client->getImage64(array("img_id" => $image_id, "outw" => 200, "outh" => 200));
        header('Content-Type: image/png');
        echo base64_decode($img_base64->success);
        die();
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
				$object['children'] = array_merge($object['children'], static::generate_tree($arr, $key_parent, $key));
				$tree[] = $object;
			}
		}
		return $tree;
	}

	public static function ArrNode($id, $name, $parent, $type, $fun_id, $children=array()) {
		return array('id'=>$id, 'name'=>$name, 'parent_id'=>$parent, 'type'=>$type, 'fun_id'=>$fun_id, 'children'=>$children);
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

}

?>
