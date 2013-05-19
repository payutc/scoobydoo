<?php
require_once 'modules/Module.class.php';

class ModuleBlocage extends Module {

    protected $service = "BLOCKED";

    protected function action_index() {
        // Templates conf
        $this->view->set_template('html');
        $this->view->set_view($this->get_path_module() . "view/index.phtml");
        $this->view->add_jsfile('bootstrap/js/bootstrap.min.js');
        $this->view->add_jsfile($this->get_link_to_action("autocompletejs"));
        // Get fundations
        $fundations = $this->json_client->getFundations();
        $blocages = array();
        foreach ($fundations as $fun) {
            $fun_id = $fun->fun_id;
            $blocages[$fun_id] = $this->json_client->getAll(array("fun_id" => $fun_id));
        }

        // Pass parameters to view
        $this->view->add_param("fundations", $fundations);
        $this->view->add_param("blocages", $blocages);
        $this->view->add_param("block_url", $this->get_link_to_action("block"));
        $this->view->add_param("unblock_url", $this->get_link_to_action("unblock"));
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

    /*
        Effectue un blocage
    */
    protected function action_block() {
        global $_REQUEST;
        if (!isset($_REQUEST['usr_id']) || !isset($_REQUEST['fun_id']) || !isset($_REQUEST['raison'])) {
            $this->view->param["alert"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramétre est manquant.");
            $this->action_index();
            return;
        }
        $this->json_client->block(array("usr_id"=>$_REQUEST['usr_id'], "fun_id"=>$_REQUEST['fun_id'], "raison"=>$_REQUEST['raison']));
        $this->action_index();
    }

    /*
        Débloque le blo_id passé en parametre
    */
    protected function action_unblock() {
        global $_REQUEST;
        if (!isset($_REQUEST['blo_id']) || !isset($_REQUEST['fun_id'])) {
            $this->view->param["alert"] = array("class" => "alert alert-error", "strong" => "Erreur, ", "message" => "un paramétre est manquant.");
            $this->action_index();
            return;
        }
        $this->json_client->remove(array("blo_id"=>$_REQUEST['blo_id'], "fun_id"=>$_REQUEST['fun_id']));
        $this->action_index();
    }

    /*
        Recherche un utilisateur
        => Affiche son historique de blocage
        => Permet de le bloquer (débloquer si actuellement bloqué)
    */
    protected function action_search() {
        $this->action_index();
    }

}
?>
