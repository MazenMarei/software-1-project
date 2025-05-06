<?php

class App {

    public $controller;
    public $action;
    public $params;
    public function run() {
        $url = explode("/", trim($_SERVER["REQUEST_URI"], "/"));
        $this->controller = !empty($url[0]) ? $url[0] : "home";
        $this->action = !empty($url[1]) ? $url[1] : "index";
        $this->params = array_slice($url, 2);
    }


    public function __construct() {
        $this->run();
    }
    

    public function render() {
      if(class_exists($this->controller)) {
            $controller = new $this->controller();
            if (method_exists($controller, $this->action)) {
                call_user_func_array([$controller, $this->action], $this->params);
            } else {
                echo "Action not found!";
            }
        } else {
            echo "Controller not found!";
        }

    }
}



?>