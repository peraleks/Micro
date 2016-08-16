<?php
namespace Micro\Kernel;

use Micro\Routing\Router;
use Micro\Request\Request;

class Kernel
{
   public $request;

    public function __construct()
    {
        $this->request = new Request(Router::Instance());
        $this->request->match();
    }
}
