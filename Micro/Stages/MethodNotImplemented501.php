<?php
namespace MicroMir\Stages;

class MethodNotImplemented501
{
    public function executeStage()
    {
        $c = c();

        if (isset($c->Verbs->array[strtolower($_SERVER['REQUEST_METHOD'])])) return;

        $statusCode = 501;
        $message[] = "Метод не поддерживается сервером";
        $message[] = "Method not implemented on this server";

        ob_start();
        include MICRO_ERROR_PAGE;

        return  $c->ResponseFactory->get(ob_get_clean(), 501, 'html');
    }
}
