<?php

namespace MicroMir\Providers;

use Zend\Diactoros\ServerRequestFactory;

class RequestServiceProvaider
{
    public function getService()
    {
       return ServerRequestFactory::fromGlobals();
    }
}