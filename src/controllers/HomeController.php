<?php
namespace App\controllers;

class HomeController {
    public function index() {
        $pageTitle = "Home Page";
        $content = "Welcome to the home page!";
        require_once VIEWS .'home.php';

    }


    public function about($id) {
        $pageTitle = "About Page";
        $content = "This is the about page with ID:  $id";
        require_once VIEWS .'home.php';
    }
}
