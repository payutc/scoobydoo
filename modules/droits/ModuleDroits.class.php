<?

require_once 'modules/Module.class.php';

class ModuleDroits extends Module {

	protected function get_js_files() {
		return array('libs/jquery-1.7.2.min.js');
	}

	protected function get_css_files() {
		return array();
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

	public function has_rights() {
		global $AADMIN;
		$right=$AADMIN->get_fundations_with_right("ADMIN");
		if(count($right["success"]) > 0)
			return True;
		else
			return False;
	}
}

?>
