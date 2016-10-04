<?php
namespace MicroMir\Stage;

use MicroMir\Exception\MicroException;

class ExecuteRouteException extends MicroException
{
	protected $exceptionCode = 'ExecuteRoute';

	protected $ru = [
		0 => "Хост {0} не найден на сервере ",
		1 => "Ошибка : \"{0}\" в файле {1} строка {2} . Показана стандарная страница 404",
	
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