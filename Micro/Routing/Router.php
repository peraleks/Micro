<?php
namespace Micro\Routing;

class Router
{
    private static $instance;

    public $routes = array(
        'count' => 0,
        );

    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        // $this->url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $Router = $this;
        require_once __DIR__.'/../../../../app/routes.php';
    }

    public function add($url_route, $action)
    {
        $arr['url_route'] = $url_route;

        $arr['split'] = preg_split('#/#', $url_route, -1, PREG_SPLIT_NO_EMPTY);

        $arr['action'] = preg_split('/@/', $action, -1, PREG_SPLIT_NO_EMPTY);

        $arr['method'] = 'GET';

        $arr['regx_route'] = '#^';

        for ($i = 0; $i < count($arr['split']); ++$i) {
            if (preg_match('/^{\w+}$/', $arr['split'][$i])) {
                $arr['regx_route'] .= '/\w+';
                $arr['params'][$i] = trim($arr['split'][$i], '{}');
            } else {
                $arr['regx_route'] .= '/'.$arr['split'][$i];
            }
        }
        $arr['regx_route'] .= '(/|)$#';

        $this->routes[] = $arr;

        ++$this->routes['count'];

        return $this;
    }

    public function method($method)
    {
        $this->routes[$this->routes['count'] - 1]['method'] = $method;

        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
