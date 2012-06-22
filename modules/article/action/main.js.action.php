<?php
// Pour cette action on veut le template JS
$this->view->set_template("js");

// On veut égallement une vue particuliére
$myview = $this->view_to_filename("main.js");
$this->view->set_view($myview);

// Configuration des parametres nécessaires à la vue (les urls ajax)
global $CONF;
$url_base = $CONF["scoobydoo_url"]."?module=article&action=";
$this->view->add_param("get_tree", $url_base."get_tree");
$this->view->add_param("details_article", $url_base."details_article");
$this->view->add_param("details_categorie", $url_base."details_categorie");
$this->view->add_param("save_article", $url_base."save_article");
$this->view->add_param("save_categorie", $url_base."save_categorie");