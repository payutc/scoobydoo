<?

class View {

	public $header;
	public $footer;
	public $container;

	/**
	 * Constructeur.
	 */
	public function __construct() {
		global $CONF;
		$this->param = array();
		$this->template = "view/template/default.phtml";
		$this->view_url = "";
		$this->header = '
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">'.$CONF["title"].'</a>
          <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="icon-user"></i> Username
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="#">Profile</a></li>
              <li class="divider"></li>
              <li><a href="#">Sign Out</a></li>
            </ul>
          </div>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>';

    	$this->footer = '
      <hr>

      <footer>
        <p>&copy; '.$CONF["title"].'</p>
      </footer>';
	}

	public function get_container() {
		$param = $this->param;
		include $this->view_url;
	}

	public function set_param($param) {
		$this->param = $param;
	}

	public function set_view($url) {
		$this->view_url = $url;
	}

	public function render() {
		include $this->template;
	}

	public function set_template($template_name) {
		if ($template_name == 'ajax') {
			$this->template = 'template/ajax.phtml';
		}
		else {
			$this->template = 'template/default.phtml';
		}

		return $this;
	}

}
