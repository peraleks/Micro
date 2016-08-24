<?php
namespace Micro\Routing;

use Exception;
use Micro\Debug\Error\ErrorHandler;

class RouteException extends Exception
{
	public function __construct($m) {
		$this->message = $m;
		$this->code = 'Routing';
		ErrorHandler::instance()->microException($this);
	}
}