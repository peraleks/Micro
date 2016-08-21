<?php
namespace Micro\Routing;

class Router
{
    private static $instance;

    private $routes = [];

    private $groups = [];

    private $link;

    public static function instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {}

    public function init(array $arr) {
        $Router = $this;
        foreach ($arr as $value) {
            include $value;
        }
    }

    public function group($url = '') {
        $this->link = &$this->groups;
        $this->link[] = ['urlGroup' => $url];
        return $this;
    }

    public function groupEnd() {
        $this->link = &$this->groups;
        array_pop($this->link);
        return $this;
    }

    public function route($urlRoute, $action)
    {

        $arr['urlRoute'] = '';

        $url = '';
        if (!empty($this->groups)) {

            for ($i = 0; $i < count($this->groups); $i++) {
               $url .= $this->groups[$i]['urlGroup'];
            }
            $arr['urlRoute'] = $url;

            for ($i = count($this->groups) -1; $i >= 0; $i--) {
                if (isset($this->groups[$i]['method'])) {
                    $arr['method'] = $this->groups[$i]['method'];
                    break;
                }
            }
        }

        $arr['urlRoute'] .= $urlRoute;

        $arr['urlRoute'] == '/' ?: $arr['urlRoute'] = rtrim($arr['urlRoute'], '/');

        $splitUrl = preg_split('#/#', $arr['urlRoute'], -1, PREG_SPLIT_NO_EMPTY);

        $arr['action'] = preg_split('/@/', $action, -1, PREG_SPLIT_NO_EMPTY);

        isset($arr['method']) ?: $arr['method'] = 'GET';

        $arr['regxRoute'] = '#^';

        $this->link = &$this->routes['simple'];

        for ($i = 0; $i < count($splitUrl); ++$i) {
            if (preg_match('/^{\w+}$/', $splitUrl[$i])) {
                $this->link = &$this->routes['regx'];
                $arr['regxRoute'] .= '/\w+';
                $arr['params'][$i] = trim($splitUrl[$i], '{}');
            } else {
                $arr['regxRoute'] .= '/'.$splitUrl[$i];
            }
        }
        $arr['regxRoute'] .= '(/|)$#';

        $this->link[$arr['urlRoute']] = $arr;

        return $this;
    }

    public function method($method)
    {
        end($this->link);
        $this->link[key($this->link)]['method'] = $method;

        return $this;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
