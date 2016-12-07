<?php
namespace MicroMir\Container;

use MicroMir\Exception\MicroException;

class ContainerException extends MicroException
{
	protected $exceptionCode = 'Container';

	protected $ru = [
		0 => "Алиас {0} уже используется в контейнере. Привязка не произведена",
		1 => "Объект #0# не найден в контейнере. Возможно опечатка",
		2 => "Не совпадают {0} {1} {2}",
		3 => "Ошибка type hint {0}",
		4 => "Ошибка type hint. Класс {0} не является инстанцируемым",
//		5 => "Нет такого метода #0# в корневом объекте {1}. Метод проигнорирован. Возвращён объект {1} ",
	];

	protected $en = [
		0 => "not translated",
		1 => "not translated",
		2 => "not translated",
		3 => "not translated",
		4 => "not translated",
		5 => "not translated",
	];

	public function __construct(int $num, array $replace, $traceNumber = 0) {
		parent::__construct($num, $replace, $traceNumber);
	}
}