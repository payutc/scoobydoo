<?
/*
	Le constructeur de la classe View préconfigure la classe avec une configuration par défaut.
	Ce qui décide réellement ce que doit afficher la vue est le template. 
	==> Normalement un module, ne modifie pas le template ! (Par contre la vue peut le changer en fonction que l'on veuille une sortie json ou html par exemple)
	Les modules doivent par contre fair charger leurs propres vue à l'intérieur du template. Par l'intérmédiaire du view_url.
*/
class View {

	public $title;
	public $template_url; // URL DU TEMPLATE A CHARGER
	public $view_url; // URL DE LA VUE A CHARGER
	public $param = array(); // STOCKAGE DES PARAMETRES NECESAIRES AUX VUES
	public $js_files = array(); // Url des fichiers javascipts à charger  !! Utiliser uniquement dans les templates html... (pas en json)
	public $css_files = array(); // Url des fichiers css à charger  !! Utiliser uniquement dans les templates html... (pas en json)
	public $copyright;

	/**
	 * Constructeur.
	 */
	public function __construct() {
		global $CONF;

		$this->title = $CONF["title"];
		$this->template_url = "view/template/default.phtml";
		$this->view_url = "view/vue-test.phtml";
		$this->param = array();
		$this->copyright = $CONF["title"];
	}

	private function get_container() {
		include $this->view_url;
	}

	public function set_param($param) {
		$this->param = $param;
	}

	public function add_param($key, $param) {
		$this->param[$key] = $param;
	}

	public function get_param($key) {
		$this->param[$key];
	}

	public function set_jsfiles($param) {
		$this->js_files = $param;
	}

	public function add_jsfiles($param) {
		$this->js_files[] = $param;
	}

	public function echo_jsfiles() {
		$r = "";
		foreach($this->js_files as $js) {
			$r.='<script src="'.$js.'"></script>';
		}
		echo $r;
	}

	public function set_cssfiles($param) {
		$this->css_files = $param;
	}

	public function add_cssfiles($param) {
		$this->css_files[] = $param;
	}

	public function echo_cssfiles() {
		$r = "";
		foreach($this->css_files as $css) {
			$r.='<link href="'.$css.'" rel="stylesheet">';
		}
		echo $r;
	}

	public function set_view($url) {
		$this->view_url = $url;
	}

	public function render() {
		include $this->template_url;
	}

}