<?php
namespace MicroMir\Routing;

use MicroMir\Exception\MicroException;

class RouterHostException extends MicroException
{
	protected $exceptionCode = 'RouterHost';

	protected $ru = [
		0 => "Дублирование имени роутера. {0} проигнорирован ",
		1 => "Список маршрутов для {0} уже определён. {1} проигнорирован ",
		2 => "Имя хоста уже привязано. {0} проигнорирован ",
	
	];

	protected $en = [
		0 => "not translated",
		1 => "not translated",
		2 => "not translated",
		3 => "not translated",
	];

	public function __construct(int $num, array $replace, $traceNumber = 0) {
		parent::__construct($num, $replace, $traceNumber);
	}
}