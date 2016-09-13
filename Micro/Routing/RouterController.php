<?php
namespace MicroMir\Routing;

class RouterController
{
    private $route;

    private $mgs;

    private $R;

    public function __construct($R, &$mgs)
    {
        $this->R = $R;
        $this->mgs = $mgs;
    }

    public function match(Router $router)
    {
        $this->route
        =
        $router->matchUrl(urldecode($_SERVER['REQUEST_URI']), $_SERVER['REQUEST_METHOD']);
        \d::p($this->route);

        if (array_key_exists('404', $this->route)) {

            if (!headers_sent()) {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
            }
            if (!array_key_exists('controller', $this->route)) {
               $this->default404();
               return;
            }
            if ($this->route['action']) {
                try
                {
                    $action = $this->route['action'];
                    (new $this->route['controller'])->$action($this->route['params']);
                }
                catch (\Error $e)
                {
                    $this->default404();
                    new RouteException(27, [$e->getMessage(), $e->getFile(), $e->getLine()], '404');
                }
                return;
            }
            if (preg_match("/(.+?\.html|.+?\.htm)$/", $this->route['controller'])) {
                if (readfile($this->mgs['WEB_DIR'].$this->route['controller'])) {
                    return;
                }
            }
            $this->default404();
        }
        else {
            $action = $this->route['action'];
            (new $this->route['controller'])->$action($this->R, $this->route['params']);
        }
    }

    private function default404() {

        $this->mgs['LOCALE'] == 'en'
        ?
        $message = "There's nothing here"
        :
        $message = 'Здесь ничего нет';

        include(__DIR__.'/404.php');

        return;
    }
}
