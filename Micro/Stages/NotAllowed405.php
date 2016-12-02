<?php
namespace MicroMir\Stages;

class NotAllowed405
{
    public function __construct($R)
    {
        $this->Route           = $R->Route;
        $this->Request         = $R->Request;
        $this->ResponseFactory = $R->ResponseFactory;
    }

    public function executeStage()
    {
        if ($this->Route->code !== 405) return;

        $method = $this->Request->getMethod();

        $statusCode = 405;
        $message[] = "HTTP метод $method не поддерживается этим URL";
        $message[] = "HTTP method $method is not supported by this URL";

        ob_start();

        include MICRO_ERROR_PAGE;

        return
            $this->ResponseFactory->get(
                ob_get_clean(),
                405,
                'html'
            );
    }

}