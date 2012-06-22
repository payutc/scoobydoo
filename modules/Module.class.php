<?

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
		
		$method = $this->action_to_methodname($action);
		if (method_exists($this, $method)) {
			$this->$method($this->view);
		}
		else {
			echo '404';
			die();
		}
	}

	public function get_module_name() {
		$classname = get_class($this);
		return strtolower(substr($classname, strlen('module')));
	}
	
	public function get_path_module() {
		return 'modules/'.$this->get_module_name().'/';
	}

	public function action_to_methodname($action) {
		return 'action'.ucfirst($action);
	}

}

?>
