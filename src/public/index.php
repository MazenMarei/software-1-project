<?php

    define("DS", DIRECTORY_SEPARATOR);
    define("PS", PATH_SEPARATOR);
    define('APP', dirname(__DIR__).DS);
    define('VENDOR',dirname(__DIR__, 2).DS.'vendor'.DS);
    define("VIEWS", APP.'views'.DS);


    require_once VENDOR.'autoload.php';
    

    use App\core\App;

    $app = new App();
    $app->run();


?>