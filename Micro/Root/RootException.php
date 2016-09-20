<?php
namespace MicroMir\Root;

use MicroMir\Exception\MicroException;

class RootException extends MicroException
{
	protected $exceptionCode = 'Root';

	protected $ru = [
		0 => "Имя уже используется. Метод link({0}, .....) проигнорирован",
		1 => "Объект #0# не найден в корневом реестре. Возможно опечатка",
		2 => "Mетод {0} проигнорирован. Т.к. объект #1# не найден в корневом реестре",
		3 => "Метод {0} требует {1} аргумента: {2}. Метод проигнорирован",
		4 => "Mетод {0} проигнорирован. Т.к. связанный объект #1# не найден в корневом реестре",
		5 => "Нет такого метода #0# в корневом объекте {1}. Метод проигнорирован. Возвращён объект {1} ",
	];

	protected $en = [
		0 => "not translated",
		1 => "not translated",
		2 => "not translated",
		3 => "not translated",
		4 => "not translated",
		5 => "not translated",
	];

	public function __construct(int $num, array $m, $traceNumber = 0) {
		parent::__construct($num, $m, $traceNumber);
	}
}