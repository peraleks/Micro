<?php
namespace MicroMir\Stages;

class FillRoute
{
	public function __construct($R)
	{
		$this->RouterHost = $R->RouterHost;
		$this->Route 	  = $R->Route;
		$this->Request 	  = $R->Request;
		$this->ResponseFactory = $R->ResponseFactory;
	}

	public function executeStage()
	{
		$host = $this->Request->getUri()->getHost();

		if (! $router = $this->RouterHost->getRouterByHost($host)) {

			$statusCode = 404;
			$message[] = "Сайт $host не найден на этом сервере";
			$message[] = "Website $host not found on this server";

			ob_start();

			include MICRO_ERROR_PAGE;

			return 
			$this->ResponseFactory->get(
				ob_get_clean(),
				404,
				'html'
			);
		}

		if (($method = $this->Request->getMethod()) == 'HEAD') {
			$method = 'GET';
		}

		$this->Route->set(
			$router->matchUrl($this->Request->getUri()->getPath(), $method)
		);
	}
}