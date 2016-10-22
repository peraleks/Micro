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
		$this->stages = array_reverse($this->stages);

		$this->stagesPop();

	}

	private function stagesPop()
	{
		if (! $stage = array_pop($this->stages)) return;

		if ($this->R->$stage->performStage()) return;

		$this->stagesPop();
	}
}