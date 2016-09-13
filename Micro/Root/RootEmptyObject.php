<?php
namespace MicroMir\Root;

class RootEmptyObject
{
	private $objName;

	public function __construct($objName = '') {
		$this->objName = $objName;
	}

	public function __call($name, $params) {
		new RootException(2, [$name, $this->objName]);
		return $this;
	}

	public function __get($name) {
		new RootException(2, [$name, $this->objName]);
		return $this;
	}

}