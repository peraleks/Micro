<?php
namespace MicroMir\Stage;

class StageController
{
	private $stages = [];

	private $indicator;

	public function __construct($R)
	{
		$this->R = $R;
	}

	public function add($name)
	{
		if (array_key_exists($name, $this->stages)) {
			new StageControllerException(0, [$name]);
			return $this;
		}
		$this->stages[$name] = $name;

		return $this;
	}

	public function run()
	{
		foreach ($this->stages as $StagesValue) {
			$this->R->$StagesValue->performStage();
		}
	}
}