<?php
namespace Micro\Routing;
use Exception;
class RouteException extends Exception
{

	public function __construct($m) {
		$this->message = $m;
		echo $m;
            $arr = $this->getTrace()[0];
            echo "{$arr['file']}::{$arr['line']}";
		// \d::p($this);
	}
	public function show() {
			// echo $this->getMessage().'<br>';
	}
}