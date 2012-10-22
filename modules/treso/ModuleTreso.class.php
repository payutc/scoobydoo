<?php

require_once 'modules/Module.class.php';

class ModuleTreso extends Module {

	protected function action_index() {
		global $CONF;

		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/index.phtml");

		$this->view->add_jsfile('libs/underscore.js');
		$this->view->add_jsfile('modules/treso/res/main.js');
		$this->view->add_jsfile('libs/jquery-ui.js');

		$Stats = @new SoapClient($CONF['soap_url'].'STATS.class.php?wsdl');
		$articles = $Stats->get_articles(); $articles = $articles['success'];
		$users = $Stats->get_users(); $users = $users['success'];
		$transactions = $Stats->get_transactions(); $transactions = $transactions['success'];

		$this->view->add_param("articles", $articles);
		$this->view->add_param("users", $users);
		$this->view->add_param("transactions", $transactions);

		if (isset($_GET["day"]) && isset($_GET["month"]) && isset($_GET["year"]) ) {
			$summary = $Stats->get_summary_for_accounting(intval($_GET["day"]), intval($_GET["month"]), intval($_GET["year"]));
		}
		$this->view->add_param("summary", $summary);
	}

}

?>
