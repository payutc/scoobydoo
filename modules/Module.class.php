<?

class Module {
	protected $view;
	
	public function __construct(&$view) {
		$this->view = $view;
	}

	public function execute() {
		$view_template = null;
		$filename_action = null;
		$action = null;

		/* Define output template
		On choisit si l'on veut une sortie ajax (json) ou un autre sortie (on pourrait ajouter un template de debug par exemple pour dev...)
		Un template par defaut correspondant au site est appliqué par défaut.
		*/
		if (isset($_GET['template']) && file_exists($this->template_to_filename($_GET['template'])))
		{
			$view_template = $_GET['template'];
		} else {
			$view_template = 'default';
		}
		$this->view->set_template($view_template);

		/* Define action
		On choisit quelle action du module on veut appeler par défaut une action index est appelé.
		*/
		if (isset($_GET['action']) && file_exists($this->action_to_filename($_GET['action'])))
		{
			$action = $_GET['action'];
		} else {
			$action = 'index';
		}

		$filename_action = $this->action_to_filename($action);

		// LOAD JS AND CSS FILES NEEDED BY THE MODULE
		foreach ($this->get_js_files() as $filename) {
			$this->view->add_jsfile($filename);
		}
		foreach ($this->get_css_files() as $filename) {
			$this->view->add_cssfile($filename);
		}

		if (!$filename_action or !file_exists($filename_action)) {
			$filename_action = $this->action_to_filename("index");
		}

		//CHARGER L'ACTION
		include $filename_action;

	}

	protected function get_js_files() { return array(); }
	protected function get_css_files() { return array(); }

	public function get_module_name() {
		$classname = get_class($this);
		return strtolower(substr($classname, strlen('module')));
	}
	
	public function get_path_module() {
		return dirname(__FILE__).'/'.$this->get_module_name().'/';
	}

	public function action_to_filename($action) {
		return $this->get_path_module().'action/'.$action.'.action.php';
	}

	public function template_to_filename($action) {
		return $this->get_path_module().'action/'.$action.'.action.php';
	}

	public function view_to_filename($action) {
		return $this->get_path_module().'view/'.$action.'.phtml';
	}

}

?>
