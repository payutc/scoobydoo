<?php

require_once 'modules/Module.class.php';

class ModuleIndex extends Module {

	protected function get_js_files() {
		return array('libs/jquery-1.7.2.min.js');
	}

	protected function get_css_files() {
		return array();
	}

	protected function action_index() {
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/index.phtml");
	}

	protected function action_debug() {
		global $AADMIN;
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/debug.phtml");
		$this->view->add_param("all_functions", $AADMIN->__getFunctions());

		$this->view->add_param("get article 597", $AADMIN->get_article(597));
		
		$login = "mguffroy";
		$this->view->add_param("getUserIDfromLogin($login)", $AADMIN->getUserIDfromLogin($login));
		$login = "puyouart";
		$this->view->add_param("getUserIDfromLogin($login)", $AADMIN->getUserIDfromLogin($login));
		$login = "trecouvr";
		$this->view->add_param("getUserIDfromLogin($login)", $AADMIN->getUserIDfromLogin($login));

		$right=$AADMIN->get_fundations_with_right("GESARTICLE");
		$this->view->add_param("get_fundations_with_right('GESARTICLE')", $right);
		$this->view->add_param("sfdsfd", count($right["success"]));


	}

	
	protected function action_debug2() {
		global $AADMIN;
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/debug.phtml");
		//$this->view->add_param("all_functions", $AADMIN->__getFunctions());
		//$this->view->add_param("add", $AADMIN->add_article("Cuve", 3000, 581, 100));

	}

	public function get_menus() {
		return ;
	}
}

?>
