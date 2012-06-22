<?

require 'modules/Module.class.php';

class ModuleArticle extends Module {

	protected function get_js_files() {
		return array('libs/tree.jquery.js', '?module=article&action=main.js');
	}

	protected function get_css_files() {
		return array(
			'css/jqtree.css',
		);
	}
}

?>
