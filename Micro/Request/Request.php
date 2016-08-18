<?php
namespace Micro\Request;

use Micro\Routing\Router;


class Request
{
    private $routes;

    private $url_path;

    public function __construct(Router $router)
    {
        $this->url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->routes = $router->getRoutes();
        // \d::p($this->routes);
        // echo urldecode($this->url_path);
    }

    public function match()
    {
        $met = '/'.$_SERVER['REQUEST_METHOD'].'/';
        for ($i = 0; $i < $this->routes['count']; ++$i) {
            if (preg_match($this->routes[$i]['regx_route'], $this->url_path) &&
                preg_match($met, $this->routes[$i]['method'])) {
                echo $i.' --- '.$this->routes[$i]['regx_route'].'<br>';
            } else {
                echo $i.' --- 404 <br>';
            }
        }
    }
}
