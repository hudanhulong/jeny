<?php
require_once  __DIR__ . '/../vendor/autoload.php';

/*$router = $_GET['r'];

list($controllerName, $actionName) = explode('/', $router);
$ucController =ucfirst($controllerName);
$controllerName = 'jeny\\controllers\\' . $ucController. 'Controller';

$controller = new $controllerName;

return call_user_func([$controller, 'action'. ucfirst($actionName)]);
*/

$application = new sf\web\Application();
$application->run();
