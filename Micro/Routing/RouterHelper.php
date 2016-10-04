<?php
namespace MicroMir\Routing;

class RouterHelper
{

	public function __construct($R) {
		$this->Route  	  = $R->Route;
		$this->RouterHost = $R->RouterHost;
		$this->Request 	  = $R->Request;
	}

	public function getUrl($name, $routerName = null)
	{
		if (! $routerName) {
			$router	= $this->RouterHost->getRouterByHost
					  (
						$this->Request->getUri()->getHost()
					  );
		} else {
			$router = $this->RouterHost->getRouterByName($routerName);
		}

		$space = $this->Route->nSpace;

		$cntName = count($nameParts = explode('/', $name));

		if ($cntName == 1) {
			if ($space) {
				$space .= '/';
			}
			return $router->getByNamespace($space.$name)['route'];
		}

		if ($space) {
			$cntSpace = count($spaceParts = explode('/', $space));

			if ($cntSpace > ($cntName - 1)) {
				for ($i = 1; $i < $cntName; ++$i) {
					array_pop($spaceParts);
				}
				$name = implode('/', $spaceParts).'/'.$name;
			}
		}
		return $router->getByNamespace($name)['route'];
	}
}