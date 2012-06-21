<?php

class Controller {

  static public function execute(&$View)
  {
    $View->set_param(array(1=>"reer",2=>"hgghg",3=>"khjjh",4=>"hkjhk"));
    $View->set_view("view/vue-test.phtml");
  }

}

