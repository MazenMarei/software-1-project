<?php
namespace App\core;

use App\controllers\HomeController;

use App\Middlewares\Test;
use App\Middlewares\Test2;

class App {

    public function __construct() {
      
        // connect to the database
        $db = Database::getInstance();


        // Register routes
        $this->registerRoutes();

        


    }


    public function registerRoutes() {
        // Register your routes here
        Route::get('/', HomeController::class, 'index');
        Route::get('/about/{id}', HomeController::class, 'about', [Test::class, 'admin', Test2::class]);
    }


    public function run() {
        Route::dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }
}
?>