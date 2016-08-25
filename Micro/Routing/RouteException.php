<?php
namespace Micro\Routing;

use Micro\{
	Exception\MicroException,
	Debug\Error\ErrorHandler
};

class RouteException extends MicroException
{
	protected $exceptionCode = 'Routing';

	protected $ru = [
		1 => "Дублирование имени параметра \" {0} \" в маршруте {1}",
		2 => "Дублирование имени маршута \" {0} \"",
		3 => "Не определён Kонтроллер для маршута \" {0} \"",
		4 => "Метод {0} проигнорирован. Причина: не определён Kонтроллер",
		5 => "Не закрыта группа \" {0} \" {1} в списке маршрутов",
		6 => "Лишний {0}",
		7 => "Метод {0} проигнорирован. Причина: не найден route()",
		8 => "Не удалось подключить файл {0}",
	];

	protected $en = [
		1 => "Duplicate parameter name \" {0} \" in a route {1}",
		2 => "Duplicate route name \" {0} \"",
		3 => "Not defined Controller for the route \" {0} \"",
		4 => "The method {0} is ignored. Reason: the Controller isn't defined",
		5 => "Not closed group \" {0} \" {1} in the list of routes",
		6 => "Excess {0}",
		7 => "Method {0} ignored. Reason: not found route()",
		8 => "Unable to include file {0}",
	];

	public function __construct(int $num, array $m, $traceNumber = 0) {
		parent::__construct($num, $m, $traceNumber);
		// \d::p($this);
	}
}