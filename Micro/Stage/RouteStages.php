<?php
namespace MicroMir\Stage;

class RouteStages
{
	public function __construct($R)
	{
		$this->StageController = $R->StageConrtoller;
		$this->Route 		   = $R->Route;
		$this->Request 		   = $R->Request;
		$this->ResponseFactory = $R->ResponseFactory;	
	}

	public function performStage()
	{
		if ($this->Router->empty()) {
			// do something
		}

		if (isset($this->Route->stages)) {
			$routeStages = array_reverse($this->Route->stages);

			foreach ($routeStages as $RouteStagesValue) {
				$this->StageController->stage($RouteStagesValue);
			}
		}
	}
}