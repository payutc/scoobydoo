<?php

class Controller {
	protected $view;
	public function __construct(&$view) {
		$this->view = $view;
	}
	
	public function execute()
	{
		if (isset($_GET['module'])) {
			$module = $this->get_module($_GET['module']);
		} else {
			$module = $this->get_module('index');
		}

		if (!$module) {
			$module = $this->get_module('index');
		}

		$module->execute();
	}

	public function set_menu()
	{
		/*$menus = array(
                        array("content" => "Index", "class"=>"", "link"=>"#"),
                        array("content" => "Articles", "class"=>"", "link"=>"#", "submenu"=> array(
                              array("content" => "Ajouter un article", "class"=>"", "link"=>"#"), 
                              array("content" => "Ajouter une catégorie", "class"=>"", "link"=>"#"),
                              array("content" => "", "class"=>"divider", "link"=>"#"),
                              array("content" => "My divided content", "class"=>"", "link"=>"#"))),
                        array("content" => "Droits", "class"=>"", "link"=>"#")
                      );*/
		$menus = array();
		foreach($this->get_all_modules() as $modulename)
		{
			$module= $this->get_module($modulename);
			$menus[]=$module->get_menus();
		}
		$this->view->set_menu($menus);
		$this->menu_index = $this->get_module("index")->get_link_to_action("index");
	}

	public function get_all_modules() {
		$dirs = array_filter(glob('modules/*'), 'is_dir');
		$return=array();
		foreach($dirs as $module) {
			$return[]=substr($module, strlen('modules/'));
		}
		return $return;
	}

	protected function get_module($modulename) {
		$moduleclassname = $this->modulename_to_classname($modulename);
		$modulefilepath = $this->modulename_to_modulepath($modulename).$this->modulename_to_classfilename($modulename);
		$module = null;
		
		if (file_exists($modulefilepath)) {
			require_once $modulefilepath;
			$module = new $moduleclassname($this->view);
		}
		
		return $module;
	}

	public function modulename_to_classfilename($modulename) {
		return $this->modulename_to_classname($modulename).'.class.php';
	}

	public function modulename_to_modulepath($modulename) {
		return 'modules/'.$modulename.'/';
	}

	public function modulename_to_classname($modulename) {
		return 'Module'.ucfirst($modulename);
	}

}

