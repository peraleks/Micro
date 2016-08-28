<?php
namespace MicroMir\Routing;

class Router
{
    private static $instance;

    private $routes = [];

    private $groups = [];

    private $simple = [];

    private $regex  = [];

    private $name   = [];

    private $methods = ['GET','POST'];

    private $safeMode;

    private $routeFiles = [];

    private $last = false;



    public static function instance()
    {
        self::$instance
        ?:
        self::$instance = new self;

        return self::$instance;
    }

    private function __construct() {}

    public function init(array $arr, $safe = 1)
    {
        $safe == 'notSafe'
        ?
        $this->safeMode = 0
        :
        $this->safeMode = 1;
        
        foreach ($arr as $path) {
            $this->inclusion($path);
        }
        if ($this->safeMode) {
            $this->checkRegex();
        }
    }

    private function  includeFile($path)
    {
        $this->inclusion($path);
        return $this;
    }

    private function  inclusion($path)
    {
        if ($this->safeMode) {
            if (!is_readable($path)) {
                new RouteException(8, [$path], 1);
                return;

            } elseif (mime_content_type ($path) != 'text/x-php') {
                new RouteException(10, [$path], 1);
                return;
            }
        }
        $Router = $this;

        if (array_key_exists($path, $this->routeFiles)) {
            new RouteException(12, [$path]);
        }
        else {
            $this->routeFiles[$path] = $this->safeMode;
            ++$this->safeMode;
            $this->groups[$path] = [];
            end($this->groups);

            try{
                include $path;

            } catch (\Error $e) {
                new RouteException(11, [$e->getMessage(), $e->getFile(), $e->getLine()]);
                --$this->safeMode;
                unset($this->groups[$path]);
                end($this->groups);
                array_pop($this->routeFiles);
                return $this;
            }
            if($this->safeMode) {
                $this->checkRouteGroup($path);
            }   
        } 
        return;
    }

    private function notSafe()
    {
        --$this->safeMode;
        return $this;
    }

    private function group($route = '')
    {
        $file = key($this->groups);
        $this->groups[$file][]['routeGroup'] = $route;
        end($this->groups[$file]);
        $this->last = 'group';

        return $this;
    }

    public function groupEnd()
    {
        $this->last = 'groupEnd';
        $file = key($this->groups);
        if (Null == array_pop($this->groups[$file]) && $this->safeMode) {
            new RouteException(6,['groupEnd()']);
        }
        if (empty($this->groups[$file]) && !$this->safeMode) {
            unset($this->groups[$file]);
            end($this->groups);
        }
        return $this;
    }

    private function controller($controller = null)
    {
        if (!$controller) {
            new RouteException(16, [__FUNCTION__.'()']);
            return $this;
        }
        if ($this->last != 'group') {
            new RouteException(15, ["controller( ' $controller ' )", 'group()']);
            return $this;
        }
        $group = &$this->groups[key($this->groups)];
        $group[count($group) - 1]['controllerGroup'] = $controller;
        // \d::p($this->groups);
        return $this;
    }

    private function route($route = null, $controller = null)
    {
        if (!$route) {
            new RouteException(16, [__FUNCTION__.'()']);
            return $this;
        }
        foreach ($this->groups as $key => $value) {
            foreach ($this->groups[$key] as $group => $value) {
                $prefixs[] = $value['routeGroup'];
                if (isset($value['controllerGroup'])) {
                    $controllers[] = $value['controllerGroup'];
                }
            }
        }
        $arr['route'] = implode('', $prefixs);

        $arr['route'] .= $route;

        $arr['route'] == '/'
        ?:
        $arr['route'] = rtrim($arr['route'], '/');

        if ($this->safeMode) {
            if (array_key_exists($arr['route'], $this->routes)) {
                new RouteException(13, [$arr['route']]);
                return $this;
            }
        }

        if ($controller) {
            $arr['controller'] = $controller;
        }
        elseif (isset($controllers)) {
            $arr['controller'] = array_pop($controllers);
        }
        else {
            new RouteException(3, [$route, 'route()']);
            return $this;
        }


        if (!strpos($arr['route'], '{')) {
            $arr['type'] = 'simple';
        }
        else {
            $arr['type'] = 'regex';
            $parts = preg_split('#/#', $arr['route'], -1, PREG_SPLIT_NO_EMPTY);

            $arr['mask'] = '#^';
            for ($i = 0; $i < count($parts); ++$i) {
                if (preg_match('/^{\w+}$/', $parts[$i])) {
                    $arr['mask'] .= "/".$r = '\w+';
                    $param = trim($parts[$i], '{}');

                    if (isset($arr['params'][$param])) {
                        new RouteException( 1, [$param, $route]);
                    }
                    $arr['params'][$param] = $i;
                    $arr['reg'][$param] = $r; 
                }
                else {
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

    private function regex($regexArr = null) {
        if (!is_array($regexArr)) {
            new RouteException(21, [__FUNCTION__."( $regexArr )"]);
            return $this;
        }
        if ($this->last != 'route') {
            new RouteException(15, ['regex()', 'route()']);
            return $this;
        }
        $route = &$this->routes[key($this->routes)];
        if (!array_key_exists('mask', $route)) {
            new RouteException(19, ['regex()']);
            return $this;
        }
        foreach ($regexArr as $key => $value) {
            if (array_key_exists($key, $route['params'])) {
               $route['reg'][$key] = $value;
            }
            else {
                new RouteException(20, [$key]);
            }
        }
        $arr = $route['parts'];
        foreach ($route['params'] as $key => $value) {
            $arr[$value] = $route['reg'][$key];
        }
        $route['mask'] = "#^/".implode('/', $arr)."$#";

        return $this;
    }

    private function get($action = null, $controller = null)
    {
        if (!$action) {
            new RouteException(16, [__FUNCTION__.'()']);
            return $this;
        }
        $this->checkMethod('get', 'GET', $action, $controller);
        return $this;
    }

    private function post($action = null, $controller = null)
    {
        if (!$action) {
            new RouteException(16, [__FUNCTION__.'()']);
            return $this;
        }
        $this->checkMethod('post', 'POST', $action, $controller);
        return $this;
    }

    private function checkMethod($messMethod, $method, $action, $controller = null)
    {
        if ($this->last != 'route') {
            new RouteException(4, ["$messMethod( ' ".$action." ' )"], 1);
            return;
        }
        elseif (isset($this->routes[key($this->routes)][$method]['action'])) {
            new RouteException(14, ["$messMethod( ' ".$action." ' )"], 1);
            return;
        }
        $this->method($method, $action, $controller);
    }

    private function method($method, $action, $controller = null)
    {
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


    private function name($name = null)
    {
        if (!$name) {
            new RouteException(16, [__FUNCTION__.'()']);
            return $this;
        }
        if ($this->last == 'route') {
            if (array_key_exists($name, $this->name)) {
                new RouteException( 2,[$name]);
            }
            $last = key($this->routes);
            $this->name[$name] = &$this->routes[$last];
        } else {
            new RouteException(7, ["name( ' ".$name." ' )"]);
        }
        return $this;
    }

    private function overflow()
    {
        if (!$this->safeMode) {
            return $this;
        }
        if ($this->last != 'route') {
            new RouteException(15, ['overflow()', 'route()']);
            return $this;
        }
        $key = key($this->routes);
        if (array_key_exists('mask', $this->routes[$key])) {
            new RouteException(18, ['owerflow()']);
            return $this;
        }
        $this->routes[$key]['overflow'] = '';

        return $this;
    }

    public function __call($name, $args)
    {
        new RouteException(9, [$name.'()']);
        return $this;
    }

    private function checkRouteGroup()
    {
        $lastFile = key($this->groups);
        if (!empty($this->groups[$lastFile])) {
            $this->groupPop($lastFile);
        }
        array_pop($this->groups);
        end($this->groups);
    }

    private function groupPop($lastFile)
    {
        if ($group = array_pop($this->groups[$lastFile])) {

            isset($group['routeController'])
            ?
            $controller = $group['routeController']
            :
            $controller = '';

            new RouteException(5, [$group['routeGroup'], $controller], $lastFile);
            $this->groupPop($lastFile);
        }
    }

    private function checkRegex()
    {
        foreach ($this->methods as $method) {
            if (isset($this->simple[$method])) {
                foreach ($this->simple[$method] as $simple) {
                    if (isset($this->regex[$method])) {
                        foreach ($this->regex[$method] as $regex) {
                            if (!array_key_exists('overflow', $simple) &&
                                preg_match($regex['mask'], $simple['route']))
                            {
                                new RouteException(17, [
                                        $simple['route'],
                                        $regex['route'],
                                        $method,
                                    ], $regex['mask']);
                            }
                        }
                    }
                }
            }
        }
    }
}
