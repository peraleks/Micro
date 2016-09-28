<?php

class d
{
	public static $memory;

	public static function m()
	{
		if (!defined('MICRO_MEMORY')) {
			define('MICRO_MEMORY', memory_get_usage());
		}
		$mem = (memory_get_usage() - MICRO_MEMORY) / 1000;
		$time =	round(((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000), 1);

		$deb  = debug_backtrace();
		$file = $deb[0]['file'];
		$line = $deb[0]['line'];

		if (!array_key_exists('MICRO_LOADER', $GLOBALS)) {
			$loader = 'composer';
		}
		else {
			$loader = $GLOBALS['MICRO_LOADER'];
		}

		echo
		"<div title=\"$file::$line\" style=\"".self::$s['time_main']."\">
			<b style=\"color: green; font-size: 1.2em;\">{$loader}</b> al<br>
			<b style=\"color: blue; font-size: 1.2em;\">$time</b> ms<br>
			<b style=\"color: red; font-size: 1.2em;\">$mem</b> kb<br>
		</div>";
	}


	private function __construct($var) {}

	public static function p($var)
	{
		static $int = 0;
		
		ob_start();
		print_r($var);
		$print = htmlentities(ob_get_contents());
		ob_end_clean();

		$print = self::color($print, [

			'[' 	=> '728e72',
			']' 	=> '728e72',
			'=&gt;'	=> '6a9695',
			'(' 	=> '555',
			')' 	=> '555',
			'Array' => 'bf7279',

		]);

		$deb  = debug_backtrace();
		$file = $deb[0]['file'];
		$line = $deb[0]['line'];

		echo 
		"<div style=\"".self::$s['main']."\">
				<div style=\"".self::$s['int']."\">$int</div>&nbsp
				<div style=\"".self::$s['file']."\">$file</div>&nbsp
				<div style=\"".self::$s['line']."\">$line</div>
			<pre>
				<div style=\"".self::$s['body']."\">
					{$print}
				</div>
			</pre>
		</div>
		";

		++$int;
	}

	private static function color($print, $arr) {
		foreach ($arr as $char => $color) {
			$print = implode("<span style=\" color: #$color;\">$char</span>",
				explode($char, $print));
		}
		return $print;
	}

	private static $s = [
		'main' =>  'background-color: #333;
					display: inline-block;
					min-width: 100%;
					padding: 15px 20px;
					border-radius: 25px;
					border-bottom: 1px solid #666;
					font-family: monospace;',

		'int'  =>  'color: #efef81; 
				 	font-size: 1.7em;
				 	display: inline-block;
					border-radius: 50%;
					background-color: #555;
					text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
					padding: 0 0.5em;',

		'body' =>  'background-color: #333;
					color: #ddd;
				    font-size: 100%;
					font-family: Consolas, monospace;',

		'file' =>  'color: #00d6d2;
					display: inline-block;
					text-shadow: 2px 2px 7px rgba(0, 0, 0, 0.9), 0 0 2px rgb(0, 0, 0);
					font-size: 1.4em;',

		'line' =>  'color: #7ddba4;
					text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
					display: inline-block;
					border-radius: 50%;
					background-color: #555;
					padding: 0 0.5em;
					font-size: 1.7em;',

	'time_main' => 'display: inline-block;
					position: fixed;
					bottom: 0;
					right: 0;
					opacity: 0.9;
					border-radius: 10px;
					padding: 5px;
					font-family: monospace;
					font-size: 1.2em;
					background-color: #fff;
					box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.2);
					text-align: right',

	];

}
