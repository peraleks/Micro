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

    private $namePrefixs = [];

    private $methods = ['GET', 'POST'];

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
            $this->namePrefixs[] = '';
            end($this->namePrefixs);
            $this->last = false;

            try{
                include $path;

            } catch (\Error $e) {
                new RouteException(11, [$e->getMessage(), $e->getFile(), $e->getLine()]);
                --$this->safeMode;
                unset($this->groups[$path]);
                end($this->groups);
                array_pop($this->routeFiles);
                array_pop($this->namePrefixs);
                return $this;
            }
            if($this->safeMode) {
                $this->checkRouteGroup($path);
            }
            array_pop($this->namePrefixs);
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

        return $this;
    }

    private function route($route = null, $controller = null)
    {
        if (!$route) {
            new RouteException(16, [__FUNCTION__.'()']);
            return $this;
        }
        $prefixs[] = '';
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
            $optional = 0;

            for ($i = 0; $i < count($parts); ++$i)
            {
                if (preg_match('/^{.+}$/', $parts[$i])) {

                    $param = trim($parts[$i], '{}');

                    $param = preg_replace('/\?/','', $param, 1, $opt);

                    if ($opt) {
                        if (++$optional == 1) {
                            $arr['optional'] = $i;
                        }
                    }
                    if (!$optional) {
                        $arr['mask'] .= '/\w+';
                    }
                    $arr['parts'][$i] = '\w+';

                    if (isset($arr['params'][$param])) {
                        new RouteException( 1, [$param, $route]);
                    }
                    $arr['params'][$param] = $i;
                }
                else {
                    $arr['mask'] .= '/'.$parts[$i];
                    $arr['parts'][$i] = $parts[$i];
                }
            }
            $optional
            ?
            $arr['mask'] .= '(/(.*))?$#'
            :
            $arr['mask'] .= '$#';
        }
        if ($this->safeMode) {
            $arr['file'] = debug_backtrace()[0]['file'].'::'.debug_backtrace()[0]['line'];
        }
        $this->routes[$arr['route']] = $arr;
        end($this->routes);
        $this->last = 'route';

        return $this;
    }

    private function regex($regexArr = null)
    {
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
               $route['parts'][$route['params'][$key]] = $value;
            }
            else {
                new RouteException(20, [$key]);
            }
        }
        if (array_key_exists('optional', $route)) {
            $route['mask'] = '#^';
            for ($i = 0; $i < $route['optional']; $i++) {
                $route['mask'] .= '/'.$route['parts'][$i];
            }
            $route['mask'] .= '(/(.*))?$#';
        }
        else {
            $route['mask'] = "#^/".implode('/',  $route['parts'])."$#";
        }

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
        if (isset($this->routes[key($this->routes)][$method]['action'])) {
            new RouteException(14, ["$messMethod( ' ".$action." ' )"], 1);
            return;
        }
        $this->method($method, $action, $controller);
    }

    private function method($method, $action, $controller = null)
    {
        $last = key($this->routes);
        $lastRoute = &$this->routes[$last];
        $lastRoute[$method]['action'] = $action;

        $controller
        ?
        $lastRoute[$method]['controller'] = $controller
        :
        $lastRoute[$method]['controller'] = &$lastRoute['controller'];

        $type = $this->routes[$last]['type'];
        $this->$type[$method][$last] = &$lastRoute;
    }


    private function name($name = null)
    {
        if (!$name) {
            new RouteException(16, [__FUNCTION__.'()']);
            return $this;
        }
        if (!$this->last == 'route') {
            new RouteException(7, ["name( ' ".$name." ' )"]);
            return $this;
        }
        $route = &$this->routes[key($this->routes)];

        if ($this->safeMode && array_key_exists('name', $route)) {
            new RouteException(22, ["name(' $name ')"]);
            return $this;
        }
        $last = key($this->namePrefixs);
        if (!empty($this->namePrefixs[$last])) {
            $name = $this->namePrefixs[$last][0].$name;
        }
        if ($this->safeMode && array_key_exists($name, $this->name)) {
            new RouteException( 2,[$name]);
            return $this;
        }
        if ($this->safeMode) {
            $route['name'] = $name;
        }
        $this->name[$name] = &$route;

        return $this;
    }

    private function namePrefix($prefix = null)
    {
        if ($this->last) {
            new RouteException(15, ['namePrefix()', '$Router']);
            return $this;
        }
        $last = key($this->namePrefixs);

        if (null === $prefix) {
            !empty($this->namePrefixs)
            ?:
            $this->namePrefixs[$last][0] = $this->namePrefixs[$last - 1][0];
        }
        else {
            $this->namePrefixs[$last][0] = $prefix;
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
        $lastRoure = &$this->routes[key($this->routes)];
        if (array_key_exists('mask', $lastRoure)) {
            new RouteException(18, ['owerflow()']);
            return $this;
        }
        $lastRoure['overflow'] = '';

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


    public function matchUrl($url, $method)
    {
        $url = parse_url($url, PHP_URL_PATH);

        $url == '/'
        ?:
        $url = rtrim($url, '/');

        if (array_key_exists($url, $this->simple[$method])) {
            return [
                    'controller' => $this->simple[$method][$url][$method]['controller'],
                    'action'     => $this->simple[$method][$url][$method]['action']
                   ];
        }
        foreach ($this->regex[$method] as $regexArr) {

            if (preg_match($regexArr['mask'], $url)) {
                $urlParts = explode('/', ltrim($url, '/'));

                if (array_key_exists('optional', $regexArr)) {
                    if (($count = count($urlParts)) > count($regexArr['parts'])) {
                        return '404';
                    }
                    else {
                        for ($i = $regexArr['optional']; $i < $count; ++$i) {
                            $i.'<br>';
                            if (!preg_match('#^'.$regexArr['parts'][$i].'$#', $urlParts[$i])) {
                                return '404';
                            }
                        }
                    }
                }
                $params = [];
                foreach ($regexArr['params'] as $key => $value) {
                    if (array_key_exists($value, $urlParts)) {
                        $params[$key] = $urlParts[$value];
                    }
                    else {
                        break;
                    }
                }
                return [
                        'controller' => $regexArr[$method]['controller'],
                        'action'     => $regexArr[$method]['action'],
                        'params'     => $params
                       ];
            }
        }
        return '404';
    }

    public function list($regex = null)
    {
       include __DIR__.'/list/list.php';
       return $this;
    }
}