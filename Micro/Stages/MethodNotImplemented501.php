<?php
namespace MicroMir\Stages;

class MethodNotImplemented501
{
    public function __construct($R)
    {
    	$this->Verbs		   = $R->Verbs;
        $this->ResponseFactory = $R->ResponseFactory;
    }

    public function executeStage()
    {
        if (! array_key_exists($_SERVER['REQUEST_METHOD'], $this->Verbs->array)) {

        	$statusCode = 501;
        	$message[] = "Метод не поддерживается сервером";
        	$message[] = "Method not implemented on this server";

        	ob_start();

        	include MICRO_ERROR_PAGE;

        	return 
        	$this->ResponseFactory->get(
        		ob_get_clean(),
        		501,
        		'html'
        		// ['Content-Length' => '']
        	);
        }
    }
}
