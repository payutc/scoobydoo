<?php

require_once 'modules/Module.class.php';

class ModuleTreso extends Module {

	protected $service = "TRESO";

	protected function action_index() {
		// Get fundations
        $fundations = $this->json_client->getFundations();

		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/index.phtml");
		$this->view->add_param("fundations", $fundations);
		$this->view->add_param("url", $this->get_link_to_action("details"));
	}

	protected function action_details() {
		$fun_id = $_GET['fun_id'];
		if(empty($fun_id)) {
			return $this->super_treso();
		}
		// Get fundation
        $fundations = $this->json_client->getFundations();
		foreach($fundations as $fun) {
			if($fun->fun_id == $_GET['fun_id']) {
				$fundation = $fun;
			}
		}

		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/treso.phtml");

		$this->view->add_param("fundation", $fundation);
		$this->view->add_param("details", $this->json_client->getDetails(array("fun_id" => $fun_id)));
		$this->view->add_param("url_ask", $this->get_link_to_action("askreversement")."&fun_id=".$fun_id);
	}

	protected function action_askreversement() {
		$fun_id = $_GET['fun_id'];
		$this->json_client->askReversement(array("fun_id" => $fun_id));
		header("Location: ".$this->get_link_to_action("details")."&fun_id=".$fun_id);
		exit();
	}

	protected function super_treso() {
		$isAdmin = $this->json_client->isAdmin();
		if(!$isAdmin) {
			header("Location: ".$this->get_link_to_action("index"));
			exit();
		}

		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/supertreso.phtml");

		$this->view->add_param("details",  $this->json_client->getDetails());
		$this->view->add_param("url_rev",  $this->get_link_to_action("reversement"));
	}

	protected function action_reversement() {
		$isAdmin = $this->json_client->isAdmin();
		if(!$isAdmin) {
			header("Location: ".$this->get_link_to_action("index"));
			exit();
		}

		if(isset($_POST['rev_id'])) {
			// Do reversement
			$rev_id = $_POST['rev_id'];
			$taux = $_POST['taux'];
			$frais = $_POST['frais'];

			$this->json_client->makeReversement(array(
				"rev_id" => $rev_id,
				"taux" => $taux*100,
				"frais" => $frais*100
				));

			header("Location: ".$this->get_link_to_action("details")."&fun_id=");
			exit();
		}


		$this->view->add_param("reversement", $this->json_client->getReversement(array("rev_id" => $_GET['rev_id'])));
		$this->view->add_param("fundations", $this->json_client->getFundations());
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/reversement.phtml");
	}
}

?>
