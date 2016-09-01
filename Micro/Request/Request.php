<?php
namespace MicroMir\Request;

use MicroMir\Routing\Router;


class Request
{
    private $routes;

    private $url_path;

    public function __construct()
    {
        // echo urldecode($this->url_path);
    }

    public function match(Router $router)
    {
        $this->routes = $router->matchUrl(urldecode($_SERVER['REQUEST_URI']), $_SERVER['REQUEST_METHOD']);
        // \d::p($this->routes);
    }
}
