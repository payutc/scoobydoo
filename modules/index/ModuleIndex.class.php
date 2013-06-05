<?php

require_once 'modules/Module.class.php';

class ModuleIndex extends Module {

	protected function get_js_files() {
		return array();
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
		$this->view->add_param("get articles", $AADMIN->get_articles());
		$this->view->add_param("get categories", $AADMIN->get_categories());
		$this->view->add_param("get categorie 580", $AADMIN->get_categorie(580));
		
		$login = "mguffroy";
		$this->view->add_param("getUserIDfromLogin($login)", $AADMIN->getUserIDfromLogin($login));
		$login = "puyouart";
		$this->view->add_param("getUserIDfromLogin($login)", $AADMIN->getUserIDfromLogin($login));
		$login = "trecouvr";
		$this->view->add_param("getUserIDfromLogin($login)", $AADMIN->getUserIDfromLogin($login));

		$right=$AADMIN->get_fundations_with_right("GESARTICLE");
		$this->view->add_param("get_fundations_with_right('GESARTICLE')", $right);
		$this->view->add_param("sfdsfd", count($right["success"]));

		$this->view->add_param("t", $AADMIN->get_categories_by_fundation_id(2, true));
		$this->view->add_param("u", $AADMIN->get_categories_by_fundation_id(2, false));

	}

	
	protected function action_debug2() {
		global $AADMIN;
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/debug.phtml");
		//$this->view->add_param("all_functions", $AADMIN->__getFunctions());
		//$this->view->add_param("add", $AADMIN->add_article("Cuve", 3000, 581, 100));
		$x = $_REQUEST['x'];
		$this->view->add_param('x', $x);
		$xx = $x*100;
		$this->view->add_param('xx', $xx);
	}

	public function get_menus() {
		return ;
	}
}

?>
