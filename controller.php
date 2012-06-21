<?php

class Controller {

  protected $view;

  /**
   * Constructeur.
   */
  public function __construct(&$view) {
    global $CONF;
    $this->view = $view;

    // TODO :: Auto discover les modules
  }

  public function execute()
  {
    $this->view->set_param(array(1=>"reer",2=>"hgghg",3=>"khjjh",4=>"hkjhk"));
    $this->view->set_view("view/vue-test.phtml");
  }

}

