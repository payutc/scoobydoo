<?

class Module {
	protected $view;
	
	public function __construct(&$view) {
		$this->view = $view;
	}

	public function execute() {
		$view_template = null;
		$filename = null;
		if (isset($_GET['ajax'])) {
			$filename = $this->ajax_to_filename($_GET['ajax']);
			$view_template = 'ajax';
		}
		else if (isset($_GET['view'])) {
			$filename = $this->view_to_filename($_GET['view']);
			$view_template = 'view';
		}

		//echo $filename.'<br>';
		//echo $view_template.'<br>';
		
		if (!$filename or !file_exists($filename)) {
			echo '404';
			die();
		}
		
		$this->view->set_template($view_template);
		$this->view->set_view($filename);
	}

	public function get_module_name() {
		$classname = get_class($this);
		return strtolower(substr($classname, strlen('module')));
	}
	
	public function get_path_module() {
		return dirname(__FILE__).'/'.$this->get_module_name().'/';
	}

	public function ajax_to_filename($ajax) {
		return $this->get_path_module().'ajax/'.$ajax.'.ajax.phtml';
	}

	public function view_to_filename($ajax) {
		return $this->get_path_module().'view/'.$ajax.'.view.phtml';
	}
}

?>
