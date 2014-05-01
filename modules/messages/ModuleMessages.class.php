<?php
require_once 'modules/Module.class.php';

class ModuleMessages extends Module {

    protected $service = "MESSAGES";

    protected function action_index() {
        // Templates conf
        $this->view->set_template('html');
        $this->view->set_view($this->get_path_module() . "view/index.phtml");
        $this->view->add_jsfile('bootstrap/js/bootstrap.min.js');

        // Get fundations
        $fundations = $this->json_client->getFundations();

        // Pass parameters to view
        $this->view->add_param("fundations", $fundations);
        $this->view->add_param("fun_url", $this->get_link_to_action("fun"));

    }

    /*
        Affiche le message d’une fundation et propose de le changer
    */
    protected function action_fun() {
        global $_REQUEST;

        if (!isset($_REQUEST['name']) || !isset($_REQUEST['fun_id'])) {
            $this->view->param["alert"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramètre est manquant.");
            $this->action_index();
            return;
        }
        // Templates conf
        $this->view->set_template('html');
        $this->view->set_view($this->get_path_module() . "view/fun.phtml");

        $msg = $this->json_client->getMsg(array("fun_id"=>$_REQUEST['fun_id'], "usr_id"=>null));

        // Pass parameters to view
        $this->view->add_param("current_msg", $msg);
        $this->view->add_param("fun_id", $_REQUEST['fun_id']);
        $this->view->add_param("set_url", $this->get_link_to_action("set_message"));

    }

    /*
         Change le message d’une fundation
    */
    protected function action_set_message() {
        global $_REQUEST;
        if (!isset($_REQUEST['message']) || !isset($_REQUEST['fun_id'])) {
            $this->view->param["alert"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramètre est manquant.");
            $this->action_index();
            return;
        }
        $message = htmlspecialchars($_REQUEST['message']);
        $this->json_client->changeMsg(array("message"=>$message, "fun_id"=>$_REQUEST['fun_id']));
        $this->action_index();
    }
}
?>
