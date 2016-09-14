<?php
namespace MicroMir\Routing;

class RouterHelper
{
	private $nSpace;

	private $mgs;

	public function __construct($R, &$mgs) {
		$this->mgs = $mgs;
		$this->R = $R;
	}

	public function space() {
		return $this->R->RouterController->getSpace();
	}

}