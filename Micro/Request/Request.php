<?php
namespace MicroMir\Request;

use MicroMir\Routing\Router;


class Request
{
    private $routes;

    private $url_path;

    public function __construct()
    {
    }

    public function match(Router $router)
    {
        $this->routes = $router->matchUrl(urldecode($_SERVER['REQUEST_URI']),
                                                    $_SERVER['REQUEST_METHOD']);
        if ($this->routes == '404') {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        }
        \d::p($this->routes);
    }
}
