<?php
namespace MicroMir\Settings;

class Settings
{
	protected $Settings = [];

	protected $objName;

	public function __construct($objName, $file = null) {
		$this->objName = $objName;
		if ($file) {
			$this->set($file);
		}
	}

	public function  set($file)
	{
	    $Settings = $this;

	    try{
	        include $file;

	    } catch (\Error $e) {
	        // new SettingsException(1, [$e->getMessage(), $e->getFile(), $e->getLine()]);
	    }

	    return;
	}

    	// создаём параметр => значение
	public function __call($name, $args)
	{
	   if (isset($this->Settings[$name])) {
   	        new SettingsException(1, [$name], 1);
   	        return $this;
   	    }
        $this->Settings[$name] = $args[0];

   	    return $this;
	}
		// получаем значение параметра через свойство объекта
	public function __get($name)
	{
	    if (!isset($this->Settings[$name])) {
	    	if ($GLOBALS['MICRO_LOADER']) {
		        new SettingsException(1, [$name]);
	    	}
	    	else {
	    		// trigger_error('нет такой настройки '.$name, E_USER_ERROR);
	    	}
	        return null;
	    }
	     if (is_callable($this->Settings[$name])) {

            $this->Settings[$name]
            =
            call_user_func($this->Settings[$name]);
        }
	    return $this->Settings[$name];
	}

	// public static function __callStatic($name, $args)
	// {
	//     new SettingsException(1, [$name]);

	// }

}