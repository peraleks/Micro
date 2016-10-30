<?php
namespace MicroMir\Stages;

class FollowRoute
{
	public function __construct($R)
	{
		$this->R 	 = $R;
		$this->Route = $R->Route;
		$this->ResponseFactory = $R->ResponseFactory;
	}

	public function executeStage()
	{
		$GLOBALS['MICRO_ERROR_MARKER'] = 1;

		$action = $this->Route->action;

		(new $this->Route->controller($this->R, $this->Route->params))
			->$action($this->R, $this->Route->params);
	}
}