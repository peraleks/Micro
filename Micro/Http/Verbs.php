<?php
namespace MicroMir\Http;

class Verbs
{
	public $array = [];

	public function __construct()
    {
        $array = include MICRO_VERBS_SETTINGS;

        foreach ($array as $key => $value) {
            $this->array[strtolower($value)] = $value;
        }
    }

}