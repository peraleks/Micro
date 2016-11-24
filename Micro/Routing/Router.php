<?php
namespace MicroMir\Routing;

class Router
{
    private $methods   = [];

    private $routes    = [];

    private $simple    = [];

    private $regex     = [];

    private $name      = [];

    private $urlNodes  = [];

    private $nameSpace = [];

    private $controllerGroup;

    private $controllerSpace = [];

    private $safeMode;

    private $routeFiles = [];

    private $page404 = [];

    private $last = false;


    public function __construct(array $verbs, array $routePaths, $safe = '')
    {
        $this->methods = $verbs;

        $safe == 'notSafe'
        ?
        $this->safeMode = 0
        :
        $this->safeMode = 1;

        $this->page404['code404'] = '';
        $this->page404['nSpace']  = '';

        foreach ($routePaths as $path) {
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
                new RouterException(8, [$path], 1);
                return;

            } elseif (mime_content_type ($path) != 'text/x-php') {
                new RouterException(10, [$path], 1);
                return;
            }
        }
        $Router = $this;

        if ($this->safeMode) {

            if (array_key_exists($path, $this->routeFiles)) {
                new RouterException(12, [$path]);
                return;
            }
            if (!empty($this->controllerGroup)) {
                new RouterException(25, [$this->controllerGroup], 1);
                return;
            }
        }
        $this->routeFiles[$path] = $this->safeMode;

        ++$this->safeMode;

            $this->urlNodes[$path] = [];
        end($this->urlNodes);

            $this->nameSpace[$path] = [];
        end($this->nameSpace);

        $this->controllerSpace[] = '';

        $this->last = false;

        try{
            include $path;

        } catch (\Error $e) {
            new RouterException(11, [$e->getMessage(), $e->getFile(), $e->getLine()]);

            --$this->safeMode;

            array_pop($this->routeFiles);

            unset($this->urlNodes[$path]);
              end($this->urlNodes);

            unset($this->nameSpace[$path]);
              end($this->nameSpace);

            array_pop($this->controllerSpace);

            return $this;
        }

        $this->checkGroup($this->urlNodes, 'node');
        $this->checkGroup($this->nameSpace, 'nameSpace');
        if ($this->controllerGroup) {
            new RouterException(26, [$this->controllerGroup, $path], 1);
        }
                                  $file = key($this->urlNodes);
        if (empty($this->urlNodes[$file])) {
                  $this->urlNodes[$file] = '';
            unset($this->urlNodes[$file]);
        }
                                   $file = key($this->nameSpace);
        if (empty($this->nameSpace[$file])) {
                  $this->nameSpace[$file] = '';
            unset($this->nameSpace[$file]);
        }
        array_pop($this->controllerSpace);
        $this->controllerGroup = null;
        return;
    }

    private function notSafe()
    {
        --$this->safeMode;
        return $this;
    }

    private function node($route = '')
    {
                            $file = key($this->urlNodes);
            $this->urlNodes[$file][] = $route;
        end($this->urlNodes[$file]);

        $this->last = 'node';

        return $this;
    }

    public function End_node()
    {
        $file = key($this->urlNodes);

        if (empty($this->urlNodes) || null === array_pop($this->urlNodes[$file])) {
            new RouterException(6,['End_node()']);
        }
        $this->last = 'End_node';

        return $this;
    }

     private function nameSpace($space = '')
     {
                             $file = key($this->nameSpace);
            $this->nameSpace[$file][] = $space;
        end($this->nameSpace[$file]);

        $this->last = 'nameSpace';

        return $this;
    }

     public function End_nameSpace()
     {
         $file = key($this->nameSpace);

         if (empty($this->nameSpace) || null === array_pop($this->nameSpace[$file])) {
             new RouterException(6,['End_nameSpace()']);
         }
         $this->last = 'End_nameSpace';

         return $this;
     }

    private function controller($controller = null)
    {
        if ($this->safeMode) {

            if (!$controller) {
                new RouterException(16, [__FUNCTION__.'()']);
                return $this;
            }
            if (!empty($this->controllerGroup)) {
                new RouterException(24, [$this->controllerGroup]);
                return $this;
            }
        }
        $this->controllerGroup = $controller;

        return $this;
    }

    private function End_controller($value = null) {
        if ($this->controllerGroup === null) {
            new RouterException(6, ["End_controller('$value')"]);
        }
        $this->controllerGroup = null;

        return $this;
    }

    private function controllerSpace($space = null)
    {
        if ($this->safeMode) {
            if ($this->last) {
                new RouterException(15, ['controllerSpace()', '$Router']);
                return $this;
            }
            if (!$space) {
                new RouterException(16, [__FUNCTION__.'()']);
                return $this;
            }
        }
        $this->controllerSpace[count($this->controllerSpace) - 1] = $space.'\\';

        return $this;
    }

    private function route($route = null, $controller = null)
    {
        if ($this->safeMode && !$route) {
            new RouterException(16, [__FUNCTION__.'()']);
            return $this;
        }
        $nodes[] = '';
        foreach ($this->urlNodes as $File) {
            foreach ($File as $Node) {
                $nodes[] = $Node;
            }
        }
        $arr['route'] = implode('', $nodes);
        $arr['route'] .= $route;

        $arr['route'] == '/'
        ?:
        $arr['route'] = rtrim($arr['route'], '/');

        if ($this->safeMode) {
            if (array_key_exists($arr['route'], $this->routes)) {
                new RouterException(13, [$arr['route']]);
                return $this;
            }
        }

        if ($controller) {
            $arr['controller'] = end($this->controllerSpace).$controller;
        }
        elseif ($this->controllerGroup) {
            $arr['controller'] = end($this->controllerSpace).$this->controllerGroup;
        }
        else {
            new RouterException(3, [$route, 'route()']);
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
                        $arr['mask'] .= '/.+';
                    }
                    $arr['parts'][$i] = '.+';

                    if (isset($arr['params'][$param])) {
                        new RouterException( 1, [$param, $route]);
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
            new RouterException(21, [__FUNCTION__."( $regexArr )"]);
            return $this;
        }
        if ($this->last != 'route') {
            new RouterException(15, ['regex()', 'route()']);
            return $this;
        }
        $route = &$this->routes[key($this->routes)];
        if (!array_key_exists('mask', $route)) {
            new RouterException(19, ['regex()']);
            return $this;
        }
        foreach ($regexArr as $key => $value) {
            if (array_key_exists($key, $route['params'])) {
               $route['parts'][$route['params'][$key]] = $value;
            }
            else {
                new RouterException(20, [$key]);
            }
        }
        if (array_key_exists('optional', $route)) {
            $route['mask'] = '#^';
            for ($i = 0; $i < $route['optional']; ++$i) {
                $route['mask'] .= '/'.$route['parts'][$i];
            }
            $route['mask'] .= '(/(.*))?$#';
        }
        else {
            $route['mask'] = "#^/".implode('/',  $route['parts'])."$#";
        }
        return $this;
    }

    public function __call($name, $args)
    {
        if (! array_key_exists($name, $this->methods)) {
            new RouterException(9, ['->'.$name.'()']);
            return $this;
        }

            //$args[0] - action, $args[1] - controller
        if (! isset($args[0])) {
            new RouterException(16, ['->'.$name.'()']);
            return $this;
        }

        $this->checkMethod($name, $args[0], isset($args[1]) ? $args[1] : null);
        return $this;
    }

    private function checkMethod($method, $action, $controller)
    {
        if ($this->last != 'route') {
            new RouterException(4, ['->'."$method('".$action."')"], 1);
            return;
        }
        if (isset($this->routes[key($this->routes)][$method]['action'])) {
            new RouterException(14, ['->'."$method('".$action."')"], 1);
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
        $lastRoute[$method]['controller'] = end($this->controllerSpace).$controller
        :
        $lastRoute[$method]['controller'] = &$lastRoute['controller'];

        $type = $this->routes[$last]['type'];
        $this->$type[$method][$last] = &$lastRoute;
    }


    private function name($name = null)
    {
        if (!$name) {
            new RouterException(16, [__FUNCTION__.'()']);
            return $this;
        }
        if ($this->last != 'route') {
            new RouterException(7, ["name('".$name."')"]);
            return $this;
        }
        $route = &$this->routes[key($this->routes)];

        if ($this->safeMode && array_key_exists('name', $route)) {
            new RouterException(22, ["name('$name')"]);
            return $this;
        }
        foreach ($this->nameSpace as $File) {
            foreach ($File as $NameSpaceValue) {
                $spaceParts[] = $NameSpaceValue;
            }
        }
        if (isset($spaceParts)) {

            $nSpace = implode('/', $spaceParts);
            $fullSpace = $nSpace.'/'.$name;

            if ($this->safeMode && array_key_exists($fullSpace, $this->name)) {
                new RouterException( 2,[$fullSpace]);
                return $this;
            }
            $route['nSpace'] = $nSpace;

            $this->name[$fullSpace] = &$route;
        }
        else {
            $this->name[$name] = &$route;
        }
        $route['name'] = $name;


        return $this;
    }


    private function overflow()
    {
        if (!$this->safeMode) {
            return $this;
        }
        if ($this->last != 'route') {
            new RouterException(15, ['overflow()', 'route()']);
            return $this;
        }
        $lastRoure = &$this->routes[key($this->routes)];
        if (array_key_exists('mask', $lastRoure)) {
            new RouterException(18, ['owerflow()']);
            return $this;
        }
        $lastRoure['overflow'] = '';

        return $this;
    }

    private function checkGroup(&$group, $groupName)
    {
        $lastFile = key($group);
        if (!empty($group[$lastFile])) {
            $this->groupPop($lastFile, $group, $groupName);
        }
        prev($group);
    }

    private function groupPop($lastFile, &$group, $groupName)
    {
        if ($groupValue = array_pop($group[$lastFile])) {
            new RouterException(5, [$groupName.'(\''.$groupValue.'\')'], $lastFile);
            $this->groupPop($lastFile, $group, $groupName);
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
                                new RouterException(17, [
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

    private function page404($controller = null, $action = null)
    {
        if (!$controller) {
            new RouterException(16, [__FUNCTION__.'()']);
            return $this;
        }
        if ($this->safeMode) {

            $this->page404['file']
            =
            debug_backtrace()[0]['file'].'::'.debug_backtrace()[0]['line'];
        }
        $this->page404['controller'] = $controller;
        $this->page404['action']     = $action;

        return $this;
    }

    public function matchUrl($url, $method)
    {
        $url == '/'
        ?:
        $url = rtrim($url, '/');

        if (array_key_exists($method, $this->simple)) {

            if (array_key_exists($url, $this->simple[$method])) {
                if (array_key_exists('nSpace', $this->simple[$method][$url])) {
                    $nSpace = $this->simple[$method][$url]['nSpace'];
                }
                else {
                    $nSpace = '';
                }
                return [
                'controller' => $this->simple[$method][$url][$method]['controller'],
                'action' => $this->simple[$method][$url][$method]['action'],
                'params' => [],
                'nSpace' => $nSpace,
                ];
            }
        }
        if (!array_key_exists($method, $this->regex)) {
            return $this->page404;
        }
        foreach ($this->regex[$method] as $regexArr) {

            if (preg_match($regexArr['mask'], $url)) {
                $urlParts = explode('/', ltrim($url, '/'));

                if (array_key_exists('optional', $regexArr)) {
                    if (($count = count($urlParts)) > count($regexArr['parts'])) {
                        return $this->page404;
                    }
                    else {
                        for ($i = $regexArr['optional']; $i < $count; ++$i) {
                            if (!preg_match('#^'.$regexArr['parts'][$i].'$#', $urlParts[$i])) {
                                return $this->page404;
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
                if (array_key_exists('nSpace', $regexArr)) {
                    $nSpace = $regexArr['nSpace'];
                }
                else {
                    $nSpace = '';
                }
                return [
                        'controller' => $regexArr[$method]['controller'],
                            'action' => $regexArr[$method]['action'],
                            'params' => $params,
                            'nSpace' => $nSpace,
                       ];
            }
        }
        return $this->page404;
    }

    public function list($url = null)
    {
        if (!$url) {
            new RouterException(16, [__FUNCTION__.'()']);
            return $this;
        }
        $requestUri = parse_url(urldecode($_SERVER['REQUEST_URI']), PHP_URL_PATH);
        if ($requestUri != $url) {
            return $this;
        }
        if (array_key_exists('code404', $this->matchUrl($url, 'GET'))){
            include __DIR__.'/list/list.php';
            die();
        }
    }

    public function getByNamespace($namespace) {
        if (array_key_exists($namespace, $this->name)) {
            return $this->name[$namespace];
        }
        else {
            return false;
        }
    }
}
