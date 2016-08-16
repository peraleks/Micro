<?php

class d
{
	public static $memory;

	public static function m()
	{
		if (self::$memory == 0) {
			self::$memory = memory_get_usage();
		} else {
			echo '<b style="color: red; font-size: 1.7em;">'.
			(memory_get_usage() - self::$memory) / 1000 .'</b>'.'<t style="color: green;"> Kb</t>';
		}
	}


	public function __construct($var)
	{
		static $int = 0;
		echo '<pre><b style="color: red; font-size: 1.7em;">'.$int.'</b> ';
		var_dump($var);
		echo '</pre>';
		++$int;
	}

	public static function p($var, $param = null)
	{
		static $int = 0;
		echo '<pre><b style=" color: blue; font-size: 1.7em;">'.$int.'&nbsp&nbsp'.$param.'</b><br><hr>';
		print_r($var);
		echo '</pre>';
		++$int;
	}

	public static function d($var)
	{
		static $int = 0;
		echo '<pre><b style="color: red; font-size: 1.7em;">'.$int.'</b> ';
		var_dump($var);
		echo '</pre>';
		++$int;
	}
}
