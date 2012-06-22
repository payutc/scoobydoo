<?

require 'modules/Module.class.php';

class ModuleArticle extends Module {

	protected function get_js_files() {
		return array('libs/jquery-1.7.2.min.js', 'libs/tree.jquery.js', 'main.js');
	}

	protected function get_css_files() {
		return array(
			'css/jqtree.css',
		);
	}
}

?>
