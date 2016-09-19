<?php
namespace MicroMir\Routing;


class RouterHelper
{

	public function __construct($R) {
		$this->R = $R;
	}

	public function __call($name, $params) {
		new RouterHelperException(0, ['->'.$name.'(...)', __CLASS__]);
	}

	public static function __callStatic($name, $args) {
		new RouterHelperException(0, ['::'.$name.'(...)', __CLASS__]);
	}

	public function getUrl($name) {

		$space = $this->R->RouterController->nSpace;

		$cntName = count($nameParts = explode('/', $name));

		if ($cntName == 1) {
			if ($space) {
				$space .= '/';
			}
			return $this->R->Router->getByNamespace($space.$name)['route'];
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
		return $this->R->Router->getByNamespace($name)['route'];

	}

}