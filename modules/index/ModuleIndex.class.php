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

	public function get_menus() {
		return ;
	}
}

?>
