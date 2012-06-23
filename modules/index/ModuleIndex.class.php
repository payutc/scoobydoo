<?

require 'modules/Module.class.php';

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
}

?>
