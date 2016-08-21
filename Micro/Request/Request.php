<?php
namespace Micro\Request;

use Micro\Routing\Router;


class Request
{
    private $routes;

    private $url_path;

    public function __construct()
    {
       $this->url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
       $this->url_path == '/' ?: $this->url_path = rtrim($this->url_path, '/');
        // echo urldecode($this->url_path);
    }

    public function match(Router $router)
    {
        $this->routes = $router->getRoutes();
        $met = '/'.$_SERVER['REQUEST_METHOD'].'/';
        if (array_key_exists($this->url_path, $this->routes['simple']) &&
            preg_match($met, $this->routes['simple'][$this->url_path]['method'])) {
            echo'Совпадение '.$this->url_path.'<br>';
        }
        // for ($i = 0; $i < count($this->routes); ++$i) {
        //     if (preg_match($this->routes[$i]['regxRoute'], $this->url_path) &&
        //         preg_match($met, $this->routes[$i]['method'])) {
        //         echo $i.' --- '.$this->routes[$i]['regxRoute'].'<br>';
        //     } else {
        //         echo $i.' --- 404 <br>';
        //     }
        // }
    }
}
