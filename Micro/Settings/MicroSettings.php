<?php
namespace MicroMir\Settings;

require __DIR__.'/Settings.php';

class MicroSettings extends Settings
{
	private $PRODUCTION = 2;

	public function __cunstruct($objName, $file = null) {
		parent::__construct($objName, $file);
	}

	public function PRODUCTION() {
		if ($this->PRODUCTION == 2) {
			if (array_key_exists('DEVELOPMENT', $this->Settings)
				&&
				array_key_exists('DEVELOPMENT_IP', $this->Settings))
			{
				if ($this->Settings['DEVELOPMENT']
		            &&
		            array_key_exists($_SERVER['REMOTE_ADDR'], $this->Settings['DEVELOPMENT_IP']))
		        {
		        	$this->PRODUCTION = false;
		        } 
		        else {
		        	$this->PRODUCTION = true;
		        }   
			}
		}
		return $this->PRODUCTION;
	}

}