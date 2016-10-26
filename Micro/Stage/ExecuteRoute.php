<?php
namespace MicroMir\Stage;

class ExecuteRoute
{
	public function __construct($R)
	{
		$this->R 	 = $R;
		$this->Route = $R->Route;
		$this->Response = $R->Response;
	}

	public function performStage()
	{
		if (property_exists($this->Route, 'code404')) {

			// if (! headers_sent()) {
			// 	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			// }

			if (! property_exists($this->Route, 'controller')) {
				return $this->default404();
			}

			if ($this->Route->action) {
				try
				{
					$action = $this->Route->action;
					(new $this->Route->controller)->$action($this->Route->params);
				}
				catch (\Error $e)
				{
					return $this->default404();

					new ExecuteRouteException(1,
						[$e->getMessage(),
						$e->getFile(),
						$e->getLine()], 
						'404'
						);
				}
				return;
			}
			if (preg_match("/(.+?\.html|.+?\.htm)$/", $this->Route->controller)) {

				if ($errorPage = readfile(WEB_DIR.$this->Route->controller)) {
					return;
				}
			}
			$this->default404();
		}
		else {
			$GLOBALS['MICRO_ERROR_MARKER'] = 1;
			$action = $this->Route->action;
			(new $this->Route->controller($this->R, $this->Route->params))
			->$action($this->R, $this->Route->params);
		}
	}


	private function default404() {

		if (defined(MICRO_LOCALE) &&  MICRO_LOCALE == 'en') {

			$message = "There's nothing here";
		}
		else {
			$message = 'Здесь ничего нет';
		}

		include(__DIR__.'/404.php');

		return;
	}

}