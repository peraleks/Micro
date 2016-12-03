<?php
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use MicroMir\Debug\HtmlDumper;

class d
{
	public static $memory;

	private function __construct() {}

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


	public static function d($var)
    {
        VarDumper::setHandler(function ($var) {
            $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper();
            $dumper->dump((new VarCloner())->cloneVar($var));
        });

        static $int = 0;

        $deb  = debug_backtrace();
        $file = $deb[0]['file'];
        $line = $deb[0]['line'];

        echo
            "<div style=\"".self::$s['main']."\">
				<div style=\"".self::$s['int']."\">$int</div>
				<div style=\"".self::$s['file']."\">$file::$line</div>
		</div>
		";

        ++$int;

        VarDumper::dump($var);
    }

	public static function p($var)
	{
		static $int = 0;
		
		ob_start();
		print_r($var);
		$print = htmlentities(ob_get_contents());
		ob_end_clean();

		$print = self::color($print, [

			'[' 	=> '888',
			']' 	=> '888',
			'=&gt;'	=> '888',
			'(' 	=> '888',
			')' 	=> '888',
			'Array' => '888',

		]);

		$deb  = debug_backtrace();
		$file = $deb[0]['file'];
		$line = $deb[0]['line'];

		echo 
		"<div style=\"".self::$s['main']."\">
				<div style=\"".self::$s['int']."\">$int</div>
				<div style=\"".self::$s['file']."\">$file::$line</div>
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
		'main' =>  'background-color: #232525;
					display: inline-block;
					box-sizing: border-box;
					min-width: 100%;
					padding: 5px;
					border-radius: 5px 5px 0 0;
					border-top: 1px solid #666;
					font-size: 14px;
					font-family: Consolas, Menlo, Monaco, monospace;',

		'int'  =>  'color: #efef81; 
				 	display: inline-block;
					text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
					padding: 0 0.5em;',

		'body' =>  'background-color: #232525;
					color: #ddd;
					font-family: Consolas, Menlo, Monaco, monospace;',

		'file' =>  'color: #999;
					display: inline-block;
					text-shadow: 2px 2px 7px rgba(0, 0, 0, 0.9), 0 0 2px rgb(0, 0, 0);',

	'time_main' => 'display: inline-block;
					position: fixed;
					bottom: 0;
					right: 0;
					opacity: 0.9;
					border-radius: 10px;
					padding: 5px;
					font-family: Consolas, Menlo, Monaco, monospace;
					font-size: 14px;
					background-color: #fff;
					box-shadow: 0px 0px 10px 1px rgba(0, 0, 0, 0.2);
					text-align: right',

	];

}
