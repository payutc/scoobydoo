<?php
require_once 'modules/Module.class.php';

class ModuleDroitsv2 extends Module {

    protected $service = "ADMINRIGHT";

    protected function action_index() {
        // Recuperation des services (les services étant les droits attribuables)
        $services = $this->json_client->getServices();

        // Templates conf
        $this->view->set_template('html');
        $this->view->set_view($this->get_path_module() . "view/index.phtml");

        // autocomplete
        $this->view->add_jsfile('bootstrap/js/bootstrap.min.js');
        $this->view->add_jsfile($this->get_link_to_action("autocompletejs"));

        // Get fundations
        $fundations = $this->json_client->getFundations();
        $user_right = array();
        $app_right = array();

        foreach ($fundations as $fun_id => $fun_name) {
            $user_right[$fun_id] = $this->json_client->getUserRights(array("fun_id" => $fun_id));
            $app_right[$fun_id] = $this->json_client->getApplicationRights(array("fun_id" => $fun_id));
        }

        // Get applications
        $raw_applications = $this->json_client->getApplications();
        $applications = array();
        foreach ($raw_applications as $app) {
            $applications[$app->app_id] = $app;
        }


        // Pass parameters to view
        $this->view->add_param("fundations", $fundations);
        $this->view->add_param("user_right", $user_right);
        $this->view->add_param("app_right", $app_right);
        $this->view->add_param("services", $services);
        $this->view->add_param("applications", $applications);
        $this->view->add_param("add_user_right", $this->get_link_to_action("add_user_right"));
        $this->view->add_param("add_app_right", $this->get_link_to_action("add_app_right"));
        $this->view->add_param("remove_user_right", $this->get_link_to_action("remove_user_right"));
        $this->view->add_param("remove_app_right", $this->get_link_to_action("remove_app_right"));
    }

    protected function action_add_user_right() {
        global $_REQUEST;
        if (!isset($_REQUEST['usr_id']) || !isset($_REQUEST['right']) || !isset($_REQUEST['fun_id'])) $this->view->param["alert"]["users"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramétre est manquant.");
        else {
            try {
                $this->json_client->setUserRight(array("usr_id" => $_REQUEST['usr_id'], "service" => $_REQUEST['right'], "fun_id" => $_REQUEST['fun_id']));
                $this->view->param["alert"]["users"] = array("class" => "alert alert-success", "strong" => "Succés, ", "message" => "l'ajout du droit à réussi. ");
            }
            catch(\JsonClient\JsonException $e) {
                $this->view->param["alert"]["users"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => $e->getMessage());
            }
        }
        $this->action_index();
    }

    protected function action_add_app_right() {
        global $_REQUEST;
        if (!isset($_REQUEST['app_id']) || !isset($_REQUEST['right']) || !isset($_REQUEST['fun_id'])) $this->view->param["alert"]["applications"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramétre est manquant.");
        else {
            try {
                $this->json_client->setApplicationRight(array("app_id" => $_REQUEST['app_id'], "service" => $_REQUEST['right'], "fun_id" => $_REQUEST['fun_id']));
                $this->view->param["alert"]["applications"] = array("class" => "alert alert-success", "strong" => "Succés, ", "message" => "l'ajout du droit à réussi.");
            }
            catch(\JsonClient\JsonException $e) {
                $this->view->param["alert"]["applications"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => $e->getMessage());
            }
        }
        $this->action_index();
    }

    protected function action_remove_user_right() {
        global $_REQUEST;
        if (!isset($_REQUEST['usr_id']) || !isset($_REQUEST['right']) || !isset($_REQUEST['fun_id'])) $this->view->param["alert"]["users"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramétre est manquant.");
        else {
            try {
                $this->json_client->removeUserRight(array("usr_id" => $_REQUEST['usr_id'], "service" => $_REQUEST['right'], "fun_id" => $_REQUEST['fun_id']));
                $this->view->param["alert"]["users"] = array("class" => "alert alert-success", "strong" => "Succés, ", "message" => "la supression du droit à réussi.");
            }
            catch(\JsonClient\JsonException $e) {
                $this->view->param["alert"]["users"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => $e->getMessage());
            }
        }
        $this->action_index();
    }

    protected function action_remove_app_right() {
        global $_REQUEST;
        if (!isset($_REQUEST['app_id']) || !isset($_REQUEST['right']) || !isset($_REQUEST['fun_id'])) $this->view->param["alert"]["users"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramétre est manquant.");
        else {
            try {
                $this->json_client->removeApplicationRight(array("app_id" => $_REQUEST['app_id'], "service" => $_REQUEST['right'], "fun_id" => $_REQUEST['fun_id']));
                $this->view->param["alert"]["users"] = array("class" => "alert alert-success", "strong" => "Succés, ", "message" => "la supression du droit à réussi.");
            }
            catch(\JsonClient\JsonException $e) {
                $this->view->param["alert"]["users"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => $e->getMessage());
            }
        }
        $this->action_index();
    }

    /*
        Renvoie du json pour l'autocomplete des noms d'utilisateurs
    */
    protected function action_autocomplete() {
        global $_REQUEST;
        $this->view->set_template('json');
        isset($_REQUEST['query']) ? $queryString = $_REQUEST['query'] : $queryString = "";
        $result = $this->json_client->userAutocomplete(array("queryString" => $queryString));
        $this->view->add_param("result", $result);
    }

    /*
        Renvoie le fichier js faisant l'autocompleter
    */
    protected function action_autocompletejs() {
        $this->view->set_template('js');
        $this->view->set_view($this->get_path_module() . "view/autocomplete.js");
        $this->view->add_param("autocomplete", $this->get_link_to_action("autocomplete"));
    }

}
?>
