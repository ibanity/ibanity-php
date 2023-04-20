<?php

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$routes = [
    '/public/index.php' => 'controllers/index.php',
    '/confirmation' => 'controllers/confirmation.php',
];

function route_to_controller($uri, $routes)
{
    if (array_key_exists($uri, $routes)) {
        require $routes[$uri];
    } else {
        abort();
    }
}

function abort($code = 404)
{
    http_response_code($code);
    require "views/$code.php";
    die();
}

route_to_controller($uri, $routes);