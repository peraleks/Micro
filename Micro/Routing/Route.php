<?php
namespace MicroMir\Routing;

class Route
{
	private $route = [];

	public function __construct()
	{

	}

	public function set(array $route)
	{
		foreach ($route as $RouteKey => $RouteValue) {
			$this->$RouteKey = $RouteValue;
		}

		$this->route = $route;
	}

	public function empty()
	{
		if (empty($this->route)) {
			return true;
		}
		else {
			return false;
		}
	}
}