<?php
/**
 * List of route for example
 * $route->addRoute('GET', '/', '\App\Controllers\Dashboard/index');
 */
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $route) {

});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404 Page Not Found';
        die;
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo '405 Method Not Allowed';
        die;
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode("/", $handler, 2);
        call_user_func_array(array(new $class($twig), $method), $vars);
        break;
}
