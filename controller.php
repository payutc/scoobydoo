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

	protected function get_module($modulename) {
		$moduleclassname = $this->modulename_to_classname($modulename);
		$modulefilepath = $this->modulename_to_modulepath($modulename).$this->modulename_to_classfilename($modulename);
		$module = null;
		
		if (file_exists($modulefilepath)) {
			require $modulefilepath;
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

