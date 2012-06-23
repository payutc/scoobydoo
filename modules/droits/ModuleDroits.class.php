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
		$this->view->set_template('html');
		$this->view->set_view($this->get_path_module()."view/index.phtml");

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
		

		$this->view->add_param("set_right_fundation(9422, GESARTICLE, 2) ", $AADMIN->set_right_fundation(9422, "GESARTICLE", 2));

		$rights = array("ADMIN","GESARTICLE", "VENDRE", "TRESO");
		foreach($rights as $right)
			$this->view->add_param("get_fundations_with_right($right)", $AADMIN->get_fundations_with_right($right));

		//$this->view->add_param("add cuve", $AADMIN->add_article("Cuve", 3000, 581, 100));

	}
}

?>
