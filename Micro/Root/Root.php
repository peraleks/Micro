<?php
namespace MicroMir\Root;

use MicroMir\{
	Routing\Router,
	Request\Request
};

class Root
{
	public $router;

	public $request;

    public function __construct()
    {
        $this->router = Router::Instance();
        $this->request = new Request;
    }
}
