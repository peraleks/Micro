<?php
namespace MicroMir\Routing;


class RouterController
{
    private $route;

    private $mgs;

    public function __construct()
    {
        $this->mgs = &$GLOBALS['MICROCODER_GLOBAL_SETTINGS'];
    }

    public function match(Router $router)
    {
        $this->route = $router->matchUrl(urldecode($_SERVER['REQUEST_URI']),
                                                    $_SERVER['REQUEST_METHOD']);
        if (array_key_exists('404', $this->route)) {
            header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');

            if (!array_key_exists('controller', $this->route)) {

                $this->mgs['LOCALE'] == 'en'
                ?
                $message = "There's nothing here"
                :
                $message = 'Здесь ничего нет';

                include(__DIR__.'/404.php');

                return;
            }
            if (array_key_exists('action', $this->route)) {
                (new $this->route['controller'])->$this->route['action'];
            }
            if (preg_match("/(.+\.html| .+\.htm)$/", $this->route['controller'])) {
                readfile($this->mgs['WEB_DIR'].$this->route['controller']);
            }
        }
        else {
                $action = $this->route['action'];
                (new $this->route['controller'])->$action();
        }

        // \d::p($this->route);
    }
}
