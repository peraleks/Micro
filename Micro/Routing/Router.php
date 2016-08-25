<?php
namespace Micro\Routing;

class Router
{
    private static $instance;

    private $routes = [];

    private $groups = [];

    private $simple = [];

    private $regex  = [];

    private $name   = [];

    private $safeMode = true;

    private $last = false;



    public static function instance()
    {
        self::$instance
        ?:
        self::$instance = new self;

        return self::$instance;
    }

    private function __construct() {}

    public function init(array $arr) {
        $Router = $this;
        foreach ($arr as $value) {
            include $value;
        }
        $this->checkRouteList();
    }

    public function includeFile($path) {
        if ($this->safeMode) {
            if (!file_exists($path)) {
                new RouteException(8, [$path]);
                return $this;
            }
        }
        $Router = $this;
        include $path;

        return $this;
    }

    public function safeMode($bool = true) {
        $this->safeMode = $bool;

        return $this;
    }

    public function group($route) {
        $this->groups[]['routeGroup'] = $route;
        end($this->groups);
        $this->last = 'group';

        return $this;
    }

    public function groupEnd() {
        $this->last = 'groupEnd';
        array_pop($this->groups) !== null
        ?:
        new RouteException(6,['groupEnd()']);

        return $this;
    }

    public function controller() {
        // TODO
    }

    public function route($route, $controller = null)
    {
        $arr['route']  = '';
        $arr['controller'] = $controller;

        if (!empty($this->groups)) {
            for ($i = 0; $i < count($this->groups); $i++) {
               $arr['route'] .= $this->groups[$i]['routeGroup'];
            }

            if (!$controller) {
                for ($k = count($this->groups) - 1; $k >= 0; $k--) {
                    if (isset($this->groups[$k]['controllerGroup'])) {
                        $arr['controller'] = $this->groups[$k]['controllerGroup'];
                        break;
                    } 
                }
            }
        }
        if (!isset($arr['controller'])) {
            new RouteException(3, [$route]);

            return $this;
        }

        
        $arr['route'] .= $route;

        $arr['route'] == '/'
        ?:
        $arr['route'] = rtrim($arr['route'], '/');

        if (!strpos($arr['route'], '{')) {
            $arr['type'] = 'simple';
        }
        else {
            $arr['type'] = 'regex';
            $parts = preg_split('#/#', $arr['route'], -1, PREG_SPLIT_NO_EMPTY);

            $arr['mask'] = '#^';
            for ($i = 0; $i < count($parts); ++$i) {
                if (preg_match('/^{\w+}$/', $parts[$i])) {
                    $arr['mask'] .= '/\w+';

                    $param = trim($parts[$i], '{}');

                    if (isset($arr['params'][$param])) {
                        new RouteException( 1, [$param, $route]);
                    }

                    $arr['params'][trim($parts[$i], '{}')] = $i;

                } else {
                    $arr['mask'] .= '/'.$parts[$i];
                    $arr['parts'][$i] = $parts[$i];
                }
            }
            $arr['mask'] .= '$#';
        }
        $this->routes[$arr['route']] = $arr;
        end($this->routes);
        $this->last = 'route';

        return $this;
    }

    public function get($action, $controller = null) {
        $this->checkMethod('get', 'GET', $action, $controller);

        return $this;
    }

    public function post($action, $controller = null) {
        $this->checkMethod('post', 'POST', $action, $controller);

        return $this;
    }

    private function checkMethod($messMethod, $method, $action, $controller = null) {
        $this->last = 'route'
        ?
        $this->method($method, $action, $controller)
        :
        new RouteException(4, ["{$messMethod}( ' ".$action." ' )"], 1);
    }

    private function method($method, $action, $controller = null) {
        $last = key($this->routes);
        $this->routes[$last][$method]['action'] = $action;

        $controller
        ?
        $this->routes[$last][$method]['controller'] = $controller
        :
        $this->routes[$last][$method]['controller'] = &$this->routes[$last]['controller'];

        $type = $this->routes[$last]['type'];
        $this->$type[$method][$last] = &$this->routes[$last];
    }


    public function name($name) {
        if ($this->last == 'route') {
            $last = key($this->routes);
            if (array_key_exists($name, $this->name)) {
                new RouteException( 2,[$name]);
            }
            $this->name[$name] = &$this->routes[$last];
        } else {
            new RouteException(7, ["name( ' ".$name." ' )"]);
        }

        return $this;
    }

    private function checkRouteList() {
        if (!empty($this->groups)) {
            foreach ($this->groups as $value) {

                isset($value['routeController'])
                ?
                $controller = $value['routeController']
                :
                $controller = '';

                new RouteException(5, [$value['routeGroup'], $controller], '');
            }
        }
    }

}
