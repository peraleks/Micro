<?php
namespace MicroMir\Stages;

class NotFound404
{
	public function __construct($R)
	{
		$this->Route = $R->Route;
		$this->ResponseFactory = $R->ResponseFactory;
	}

	public function executeStage()
	{
		if (! property_exists($this->Route, 'code404')) {
			return;
		}

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

			if ($errorPage = file_get_contents(WEB_DIR.$this->Route->controller)) {
				return
				$this->ResponseFactory->get(
					$errorPage,
					404,
					'html',
					['Content-Length' => '']
				);
			}
		}

		return $this->default404();
	}


	private function default404()
	{
		$message = "Здесь ничего нет<br>There's nothing here";
		$statusCode = 404;

		ob_start();

		include MICRO_ERROR_PAGE;

		return
		$this->ResponseFactory->get(
			ob_get_clean(),
			404,
			'html',
			['Content-Length' => '']
		);
	}

}