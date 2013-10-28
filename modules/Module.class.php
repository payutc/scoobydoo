<?php

class Module {
	protected $view;
    protected $json_client;

	public function __construct(&$view) {
        global $CONF;
		$this->view = $view;
        if(isset($this->service)) {
            if(!isset($_SESSION[$this->service])) {
                $_SESSION[$this->service] = array();
            }
            if(!isset($_SESSION[$this->service]["json_client"])) {
                if(ereg('^(.*)(/)$', $CONF['soap_url'])) { 
                        $motif = ereg('^(.*)(/)$', $CONF['soap_url'], $r); 
                        $CONF['soap_url'] = $r[1]; 
                } 
                $_SESSION[$this->service]["json_client"] = new \JsonClient\AutoJsonClient($CONF['soap_url'], $this->service);
                $this->json_client = $_SESSION[$this->service]["json_client"];
            } else {
                $this->json_client = $_SESSION[$this->service]["json_client"];
            }
            if(isset($_SESSION["json_client_cookie"])) {
                $this->json_client->cookie = $_SESSION["json_client_cookie"];
            }
            if (!isset($_GET["ticket"])) {
                $this->check_json_client();
            }
        }
	}

    protected function check_json_client() {
        if(!isset($_SESSION['json_client_status']) || !$_SESSION['json_client_status']->user) {
            $_SESSION['json_client_status'] = $this->json_client->getStatus();
        }
        if (!$_SESSION['json_client_status']->user) {
            $this->auth_json_client();
        }
    }

    protected function get_cas_url() {
        if(!isset($_SESSION['cas_url'])) {
            $_SESSION['cas_url'] = $this->json_client->getCasUrl();
        }
        return $_SESSION['cas_url'];
    }

    protected function auth_json_client() {
        global $CONF;
        $cas_url = $this->get_cas_url();
        $service = $CONF['scoobydoo_url'].$this->get_link_to_action("auth_json_client");
        header("Location: ".$cas_url."login?service=".urlencode($service));
        exit();
    }

    protected function action_auth_json_client() {
        global $CONF;
        if(isset($_GET["ticket"])) {
		    $ticket = $_GET["ticket"];
            $service = $CONF['scoobydoo_url'].$this->get_link_to_action("auth_json_client");
            try {
                $con = $this->json_client->loginCas(array("ticket"=>$ticket, "service"=>$service));
            } catch (\JsonClient\JsonException $e) {
                print("<pre>"); print_r($e); print("</pre>");
                die("error login cas.");
            }
            try {
                $this->json_client->loginApp(array("key"=>$CONF['application_key']));     
            } catch (\JsonClient\JsonException $e) {
                die("error login application.");
            }
            // Save this cookie
            $_SESSION["json_client_cookie"] = $this->json_client->cookie;
            // Return to index page, the json client is authenticated.
            header("Location: ".$CONF['scoobydoo_url']);
            exit();
        } else {
            $this->auth_json_client();
        }
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

    /*
     * Return true si l'utilisateur à le droit sur le module.
     */
    public function has_rights() {
        if(isset($this->service)) {
            if(!array_key_exists('EnabledServices', $GLOBALS)) {
                $this->check_json_client();
                $GLOBALS['EnabledServices'] = $this->json_client->getEnabledServices();
            }
            return in_array($this->service, $GLOBALS['EnabledServices']);
        } else {
            return true;
        }
	}

	public function get_menus() {
		if($this->has_rights())
			return array("content" => ucfirst($this->get_module_name()), "class"=>"", "link"=>$this->get_link_to_action("index"));
		else
			return;
	}
}

?>
