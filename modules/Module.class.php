<?php

class Module {
	protected $view;
	
	public function __construct(&$view) {
		$this->view = $view;
	}

	public function execute() {
		$view_template = null;
		$filename_action = null;

		if (isset($_GET['action'])) {
			$action = $_GET['action'];
		}
		else {
			$action = 'index';
		}
		
		$method = $this->actionname_to_methodname($action);
		if (method_exists($this, $method)) {
			$this->$method();
		}
		else {
			$this->action_404();
		}
	}

	public function get_module_name() {
		$classname = get_class($this);
		return strtolower(substr($classname, strlen('module')));
	}
	
	public function get_path_module() {
		return 'modules/'.$this->get_module_name().'/';
	}

	public function actionname_to_methodname($action) {
		return 'action_'.$action;
	}

	public function action_404() {
		header("HTTP/1.0 404 Not Found");
		$this->view->set_view('modules/404.view.phtml');
	}

	public function get_link_to_action($action) {
		return "?module=".$this->get_module_name()."&action=$action";
	}

	public function has_rights() {
		return True;
	}

	public function get_menus() {
		if($this->has_rights())
			return array("content" => ucfirst($this->get_module_name()), "class"=>"", "link"=>$this->get_link_to_action("index"));
		else
			return;
	}
}

?>
