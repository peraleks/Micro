<?php
namespace MicroMir\Stages;

class RouteStages
{
	public function __construct($R)
	{
		$this->StageController = $R->StageController;
		$this->Route 		   = $R->Route;
	}

	public function executeStage()
	{
		if ($this->Route->empty()) {
			// do something
		}

		if (isset($this->Route->stages)) {
			$this->StageController->stages($this->Route->stages);
		}
	}
}