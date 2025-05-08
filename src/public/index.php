<?php

define("DS", DIRECTORY_SEPARATOR);
define("PS", PATH_SEPARATOR);
define('APP', dirname(__DIR__) . DS);
define('VENDOR', dirname(__DIR__, 2) . DS . 'vendor' . DS);
define("VIEWS", APP . 'views' . DS);
define("PUBLIC_FOLDER", APP . 'public' . DS);
define("UPLOADS", PUBLIC_FOLDER . 'uploads' . DS);


require_once VENDOR . 'autoload.php';


use App\core\App;

try {
    session_start();
    $app = new App();
    $app->run();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
