<?php
namespace MicroMir\Stages;

class FillRoute
{
    public function executeStage()
    {
        $c = c();

        $host = $c->Request->getUri()->getHost();

        if (!$router = $c->RouterHost->getRouterByHost($host)) {

            $statusCode = 404;
            $message[] = "Сайт $host не найден на этом сервере";
            $message[] = "Website $host not found on this server";

            ob_start();
            include MICRO_ERROR_PAGE;

            return $c->ResponseFactory->get(ob_get_clean(), 404, 'html');
        }
        if (($method = $c->Request->getMethod()) == 'HEAD') { $method = 'GET'; }

        $c->Route->set($router->matchUrl($c->Request->getUri()->getPath(), $method));
    }
}