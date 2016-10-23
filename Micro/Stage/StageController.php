<?php
namespace MicroMir\Stage;

class StageController
{
	private $stages = [];

	private $indicator;

	public function __construct($R)
	{
		$this->R = $R;
		$this->Emitter = $R->Emitter;
	}

	public function add($name)
	{
		$this->stages[] = $name;

		return $this;
	}

	public function afterEach($name)
	{

	}

	public function run()
	{
		$this->stages = array_reverse($this->stages);

		$this->nextStage();

	}

	private function nextStage()
	{
		if (! $stage = array_pop($this->stages)) return;

		if ($response = $this->R->$stage->performStage()) {

			$this->Emitter->emit($response);
			
			return;
		}

		$this->nextStage();
	}
}