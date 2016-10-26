<?php
namespace MicroMir\Stage;

class StageController
{
	private $stages = [];

	private $after = [];

	public function __construct($R)
	{
		$this->R = $R;
		$this->Emitter = $R->Emitter;
	}

	public function stage($name)
	{
		$this->stages[] = $name;

		return $this;
	}

	public function afterResponse($name)
	{
		$this->after[] = $name;

		return $this;
	}

	public function run()
	{
		$this->stages = array_reverse($this->stages);

		$this->nextStage();

	}

	private function nextStage()
	{
		if (! $stage = array_pop($this->stages)) {
			if (! $this->stages = array_merge($this->stages, array_reverse($this->after))) {

				return;
			}
		}
			

		if ($response = $this->R->$stage->performStage()) {
			$this->Emitter->emit($response);
			
			return;
		}

		$this->nextStage();
	}


}