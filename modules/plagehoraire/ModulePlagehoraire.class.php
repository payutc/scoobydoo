<?php

require_once 'modules/Module.class.php';

class ModulePlagehoraire extends Module {

	protected function get_js_files() {
		return array();
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
			$plages = $AADMIN->get_plages_horaire_fundation($fundation["id"]);
			$pois = $AADMIN->get_pois_fundation($fundation["id"]);
			$tab[] = array(
						"name" => $fundation["name"], 
						"id" => $fundation["id"],
						"url_add" => "?module=plagehoraire&action=add&fun_id=".$fundation["id"], 
						"url_remove" => "?module=plagehoraire&action=remove", 
						"plages"=>$plages["success"],
						"pois"=>$pois["success"]);
		}
		$this->view->add_param("tab", $tab);
	}

	protected function action_add() {
		global $_REQUEST, $AADMIN;
		if(!isset($_REQUEST['debut']) || !isset($_REQUEST['fin']) || !isset($_REQUEST['nom']) || !isset($_REQUEST['poi']))
			$this->view->param["alert"][$_REQUEST['fun_id']] = array(
				"class" => "alert alert-error",
				"strong" => "Erreur, ",
				"message" => "un paramétre est manquant."
			);
		else if(strlen($_REQUEST['debut']) != 5 || strlen($_REQUEST['fin']) != 5 ) {
			$this->view->param["alert"][$_REQUEST['fun_id']] = array(
				"class" => "alert alert-error",
				"strong" => "Erreur, ",
				"message" => "Les heures sont mal écrites..."
			);
		} else {
			$time_start=substr($_REQUEST['debut'], 0,2).substr($_REQUEST['debut'], 3,2);
			$time_end=substr($_REQUEST['fin'], 0,2).substr($_REQUEST['fin'], 3,2);
			$retour = $AADMIN->add_plage_horaire($time_start, $time_end, $_REQUEST['poi'], $_REQUEST['fun_id'], $_REQUEST['nom']);
			if(isset($retour['success']))
				$this->view->param["alert"][$_REQUEST['fun_id']] = array(
					"class" => "alert alert-success",
					"strong" => "Succés, ",
					"message" => "l'ajout de la plage à réussi."
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

	protected function action_remove() {
		global $_REQUEST, $AADMIN;
		if(!isset($_REQUEST['pla_id']))
			$this->view->param["alert"][$_REQUEST['fun_id']] = array(
				"class" => "alert alert-error",
				"strong" => "Erreur, ",
				"message" => "un paramétre est manquant."
			);
		else {
			$retour = $AADMIN->rm_plage_horaire($_REQUEST['pla_id']);
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

	public function get_menus() {
		if($this->has_rights())
			return array("content" => "Plages horaires", "class"=>"", "link"=>$this->get_link_to_action("index"));
		else
			return;
	}

	public function has_rights() {
		/*global $AADMIN;
		$right=$AADMIN->get_fundations_with_right("ADMIN");
		if(count($right["success"]) > 0)
			return True;
		else*/
			return False;
	}
}

?>
