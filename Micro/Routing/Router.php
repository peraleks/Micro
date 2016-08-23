<?php
namespace Micro\Routing;

class Router
{
    private static $instance;

    private $routes = [];

    private $groups = [];

    private $simple = [];

    private $regx = [];

    private $name = [];

    private $safeMode = true;

    private $link;



    public static function instance()
    {
        self::$instance ?: self::$instance = new self;

        return self::$instance;
    }

    private function __construct() {}

    public function init(array $arr) {
        $Router = $this;
        foreach ($arr as $value) {
            include $value;
        }
    }

    public function safeMode($bool = true) {
        $this->safeMode = $bool;

        return $this;
    }

    public function group($route = '') {
        $this->link = &$this->groups;
        $this->groups[] = ['routeGroup' => $route];

        return $this;
    }

    public function groupEnd() {
        $this->link = &$this->groups;
        array_pop($this->groups);

        return $this;
    }

    public function route($route, $controller)
    {
        $arr['route']  = '';
        if (!empty($this->groups)) {
            for ($i = 0; $i < count($this->groups); $i++) {
               $arr['route'] .= $this->groups[$i]['routeGroup'];
            }
        }
        $arr['route']  .= $route;

        if (!strpos($arr['route'], '{')) {
            $arr['type'] = 'simple';
        }
        else {
            $arr['type'] = 'regx';
            $arr['route'] = rtrim($arr['route'], '/');
            $parts = preg_split('#/#', $arr['route'], -1, PREG_SPLIT_NO_EMPTY);

            $arr['mask'] = '#^';
            for ($i = 0; $i < count($parts); ++$i) {
                if (preg_match('/^{\w+}$/', $parts[$i])) {
                    $arr['mask'] .= '/\w+';
                    $arr['params'][$i] = trim($parts[$i], '{}');
                } else {
                    $arr['mask'] .= '/'.$parts[$i];
                }
            }
            $arr['mask'] .= '$#';
        }
        $arr['controller'] = $controller;
        $this->routes[$arr['route']] = $arr;
        end($this->routes);

        return $this;
    }

    public function get($action, $controller = null) {
        $this->method('GET', $action, $controller);

        return $this;
    }

    public function post($action, $controller = null) {
        $this->method('POST', $action, $controller);

        return $this;
    }

    private function method($method, $action, $controller = null) {
        $key = key($this->routes);
        $this->routes[$key][$method]['action'] = $action;

        $controller ?
        $this->routes[$key][$method]['controller'] = $controller :
        $this->routes[$key][$method]['controller'] = &$this->routes[$key]['controller'];

        $type = $this->routes[$key]['type'];
        $this->$type[$method][$key] = &$this->routes[$key];
    }


    public function name($name) {
        $key = key($this->routes);
        if ($this->routes[$key]['type'] == 'simple') {
            if ($this->safeMode) {
                if (array_key_exists($name, $this->name)) {
                    new RouteException("Дублирование имени машрута '$name'");
                }
            } 
            $this->name[$name] = &$this->routes[$key];
        } 
        else {
            new RouteException("Именовать можно только маршруты без параметров ('$name')");
        }
        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
