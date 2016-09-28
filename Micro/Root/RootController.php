<?php
namespace MicroMir\Root;

class RootController
{
    private $route;

    public $nSpace;

    public function __construct($R)
    {
        $this->R = $R;
    }

    public function matchUrl()
    {

        $this->route
        =
        $this->R->Router->matchUrl(urldecode($_SERVER['REQUEST_URI']), $_SERVER['REQUEST_METHOD']);

        $this->nSpace = $this->route['nSpace'];
        // \d::p($this->route);

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
                    new RouterException(6, [$e->getMessage(),
                                            $e->getFile(),
                                            $e->getLine()], '404');
                }
                return;
            }
            if (preg_match("/(.+?\.html|.+?\.htm)$/", $this->route['controller'])) {
                if (readfile(WEB_DIR.$this->route['controller'])) {
                    return;
                }
            }
            $this->default404();
        }
        else {
            $GLOBALS['MICRO_ERROR_MARKER'] = 1;
            $action = $this->route['action'];
            (new $this->route['controller']($this->R, $this->route['params']))
                                  ->$action($this->R, $this->route['params']);
        }
    }

    private function default404() {

        if (defined(MICRO_LOCALE) &&  MICRO_LOCALE == 'en') {

            $message = "There's nothing here";
        }
        else {
            $message = 'Здесь ничего нет';
        }

        include(__DIR__.'/404.php');

        return;
    }
}
