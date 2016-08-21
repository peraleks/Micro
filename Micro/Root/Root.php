<?php
namespace Micro\Root;

use Micro\Routing\Router;
use Micro\Request\Request;

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
