<?

require 'modules/Module.class.php';

class ModuleDroits extends Module {

	protected function get_js_files() {
		return array('libs/jquery-1.7.2.min.js');
	}

	protected function get_css_files() {
		return array();
	}

	protected function load_menu() {
		$this->menu = "";
	}

	protected function action_index() {
		global $AADMIN;
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/index.phtml");
		$fundations = $AADMIN->get_fundations_with_right("ADMIN"); $fundations = $fundations["success"];
		$tab = array();
		foreach($fundations as $fundation)
		{
			$right = $AADMIN->get_rights_fundation($fundation["id"]);
			$tab[] = array(
						"name" => $fundation["name"], 
						"id" => $fundation["id"],
						"url_add" => "?module=droits&action=add&fun_id=".$fundation["id"], 
						"url_remove" => "?module=droits&action=remove", 
						"right"=>$right["success"]);
		}
		$this->view->add_param("tab", $tab);
	}

	protected function action_add() {
		global $_REQUEST, $AADMIN;
		if(!isset($_REQUEST['login']) || !isset($_REQUEST['right']) || !isset($_REQUEST['fun_id']))
			$this->view->param["alert"][$_REQUEST['fun_id']] = array(
				"class" => "alert alert-error",
				"strong" => "Erreur, ",
				"message" => "un paramétre est manquant."
			);
		else {
			$user_id = $AADMIN->getUserIDfromLogin($_REQUEST['login']);
			if(isset($user_id['success']))
			{
				$retour = $AADMIN->set_right_fundation($user_id['success'], $_REQUEST['right'], $_REQUEST['fun_id']);
				if(isset($retour['success']))
					$this->view->param["alert"][$_REQUEST['fun_id']] = array(
						"class" => "alert alert-success",
						"strong" => "Succés, ",
						"message" => "l'ajout du droit à réussi."
					);
				else
					$this->view->param["alert"][$_REQUEST['fun_id']] = array(
						"class" => "alert alert-error",
						"strong" => "Erreur, ",
						"message" => $retour['error_msg']
					);
			}
			else
				$this->view->param["alert"][$_REQUEST['fun_id']] = array(
					"class" => "alert alert-error",
					"strong" => "Erreur, ",
					"message" => $user_id['error_msg']
				);
		}

		$this->action_index();
	}

	protected function action_remove() {
		global $_REQUEST, $AADMIN;
		if(!isset($_REQUEST['usr_id']) || !isset($_REQUEST['rig_id']) || !isset($_REQUEST['fun_id']))
			$this->view->param["alert"][$_REQUEST['fun_id']] = array(
				"class" => "alert alert-error",
				"strong" => "Erreur, ",
				"message" => "un paramétre est manquant."
			);
		else {
			$retour = $AADMIN->remove_right_fundation($_REQUEST['usr_id'], $_REQUEST['rig_id'], $_REQUEST['fun_id']);
			if(isset($retour['success']))
				$this->view->param["alert"][$_REQUEST['fun_id']] = array(
					"class" => "alert alert-success",
					"strong" => "Succés, ",
					"message" => "la suppression du droit à réussi."
				);
			else
				$this->view->param["alert"][$_REQUEST['fun_id']] = array(
					"class" => "alert alert-error",
					"strong" => "Erreur, ",
					"message" => $retour['error_msg']
				);
		}

		$this->action_index();
	}

	protected function action_debug() {
		global $AADMIN;
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/debug.phtml");
		$this->view->add_param("all_functions", $AADMIN->__getFunctions());

		$this->view->add_param("get article 597", $AADMIN->get_article(597));
		$this->view->add_param("get article 597", $AADMIN->get_categorie(581));

		$this->view->add_param("get_rights_fundation(1)", $AADMIN->get_rights_fundation(1));
		$this->view->add_param("get_rights_fundation(2)", $AADMIN->get_rights_fundation(2));

		//$AADMIN->set_right_fundation(9420, "GESARTICLE", 2); 
		//$this->view->add_param("set_right_fundation(9422, GESARTICLE, 2) ", $AADMIN->set_right_fundation(9422, "GESARTICLE", 2));

		$rights = array("ADMIN","GESARTICLE", "VENDRE", "TRESO");
		foreach($rights as $right)
			$this->view->add_param("get_fundations_with_right($right)", $AADMIN->get_fundations_with_right($right));

		//$this->view->add_param("add cuve", $AADMIN->add_article("Cuve", 3000, 581, 100));

	}
}

?>
