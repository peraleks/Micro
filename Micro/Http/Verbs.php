<?php
namespace MicroMir\Http;

class Verbs
{
	public $array;

	public function __construct()
	{
		$this->array = array_flip(include(MICRO_VERBS_SETTINGS));
	}

}