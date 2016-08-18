<?php

class d
{
	public static $memory;

	public static function m()
	{
		if (self::$memory == 0) {
			define('MICRO_START', microtime(true));
			self::$memory = memory_get_usage();
		} else {
			$bag = debug_backtrace();
			$b = '&nbsp&nbsp<b style=" color: blue; font-size: 1em;">'.$bag[0]['file'].'</b>'.
			'::<b style=" color: green; font-size: 1em;">'.$bag[0]['line'].'</b><br>';

			echo '<div style="
								display: inline-block;
								position: fixed;
								top: 0;
								right: 0;
								text-align: right">'.
			'<b style="color: red; font-size: 1.2em;">'.
			(memory_get_usage() - self::$memory) / 1000 .
			'</b> Kb<br>'.
			'<b style="color: blue; font-size: 1.2em;">'.
			round(((microtime(true) - MICRO_START) * 1000), 1).
			'</b> ms'.
			// $b.
			'</div>';
		}
	}


	public function __construct($var)
	{
	}

	public static function p($var)
	{
		$bag = debug_backtrace();
		$b = '&nbsp&nbsp<b style=" color: blue; font-size: 1.4em;">'.$bag[0]['file'].'</b>'.
		'::<b style=" color: #00ef77; font-size: 1.7em;">'.$bag[0]['line'].'</b><br>';
		static $int = 0;
		echo '<pre><b style=" color: red; font-size: 1.7em;">'.$int.'</b>'.$b;
		print_r($var);
		echo '</pre><br><hr>';
		++$int;
	}

	public static function d($var)
	{
		$bag = debug_backtrace();
		$b = '&nbsp&nbsp<b style=" color: blue; font-size: 1.4em;">'.$bag[0]['file'].'</b>'.
		'::<b style=" color: green; font-size: 1.7em;">'.$bag[0]['line'].'</b><br>';
		static $int = 0;
		echo '<pre><b style="color: red; font-size: 1.7em;">'.$int.'</b>'.$b;
		var_dump($var);
		echo '</pre><br><hr>';
		++$int;
	}

}
