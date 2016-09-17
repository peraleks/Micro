<?php
namespace MicroMir\Settings;

use MicroMir\Exception\MicroException;

class SettingsException extends MicroException
{
	protected $exceptionCode = 'Root';

	protected $ru = [
		0 => "Имя уже используется. Метод link({0}, .....) проигнорирован",
		1 => "Объект {0} не найден в корневом реестре. Возможно опечатка",
		2 => "Mетод {0}(...) проигнорирован. Т.к. объект {1} не найден в корневом реестре",
		3 => "Объект {0} был успешно получен.<br><br> Но лучше не использовать перегрузку метода  {1}  дабы в коде визуально не путать объекты и методы.<br><br> Возможные способы доступа:<br><br> {2} - самый быстрый,&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp {3} , &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp {4}", 
	
	];

	protected $en = [
		0 => "not translated",
		1 => "not translated",
		2 => "not translated",
		3 => "not translated",
	];

	public function __construct(int $num, array $m, $traceNumber = 0) {
		parent::__construct($num, $m, $traceNumber);
	}
}