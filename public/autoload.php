<?php


define("DS" , DIRECTORY_SEPARATOR);
define("ROOT" , dirname(__DIR__). DS );
define("CONTROLLES" , ROOT  . "Controllers" . DS);
define("CORE" , ROOT . "Core" . DS);
define("VIEW" , ROOT . "Views" . DS);
$models = [ROOT, CONTROLLES , CORE];


set_include_path(get_include_path().PATH_SEPARATOR . implode(PATH_SEPARATOR, $models));
spl_autoload_register("spl_autoload");


?>

