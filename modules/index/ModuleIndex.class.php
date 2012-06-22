<?

require 'modules/Module.class.php';

class ModuleIndex extends Module {

	protected function get_js_files() {
		return array('libs/jquery-1.7.2.min.js');
	}

	protected function get_css_files() {
		return array();
	}
}

?>
