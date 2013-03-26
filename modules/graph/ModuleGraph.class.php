<?php

require_once 'modules/Module.class.php';

class ModuleGraph extends Module {

	protected function action_index() {
		global $CONF;
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/index.phtml");
		$this->view->add_jsfile('libs/sigma.min.js');
		$this->view->add_jsfile('libs/sigma.forceatlas2.js');
		$this->view->add_jsfile('libs/sigma.fisheye.js');
		$this->view->add_jsfile('modules/graph/res/main.js');

		$Stats = @new SoapClient($CONF['soap_url'].'STATS.class.php?wsdl');
		$articles = $Stats->get_articles(); $articles = $articles['success'];
		$users = $Stats->get_users(); $users = $users['success'];
		$transactions = $Stats->get_transactions(); $transactions = $transactions['success'];
		$stats = $Stats->stat_argent();

		$this->view->add_param("articles", $articles);
		$this->view->add_param("users", $users);
		$this->view->add_param("transactions", $transactions);
		$this->view->add_param("stats", $stats);

	}

	public function has_rights() {
		return False;
	}

}

?>
