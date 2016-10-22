<?php
namespace MicroMir\Stage;

class FillRoute
{
	public function __construct($R)
	{
		$this->RouterHost = $R->RouterHost->init(MICRO_DIR.'/app/config/hosts.php');
		$this->Route 	  = $R->Route;
		$this->Request 	  = $R->Request;
		// \d::p($this->Request);
	}

	public function performStage()
	{

		if (! $router
			=
			$this->RouterHost->getRouterByHost($this->Request->getUri()->getHost()))
		{
			$this->Route->set(['code404_host' => $this->Request->getUri()->getHost()]);
			return;
		}

		$this->Route->set
		(
			$router->matchUrl
			(
  			 	$this->Request->getUri()->getPath(),
  			 	$this->Request->getMethod()
  			)
		);
	}
}