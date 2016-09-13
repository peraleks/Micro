<?php
namespace MicroMir\Root;

use MicroMir\{
	Exception\MicroException,
	Debug\Error\ErrorHandler
};

class RootException extends MicroException
{
	protected $exceptionCode = 'Root';

	protected $ru = [
		0 => "Имя уже используется. Метод link({0}, .....) проигнорирован",
		1 => "Объект {0} не найден в корневом реестре. Возможно опечатка",
		2 => "Mетод {0}(...) проигнорирован. Т.к. объект {1} не найден в корневом реестре",
		3 => "Объект {0} был успешно получен.<br><br> Но лучше не использовать перегрузку метода  {1}  дабы в коде визуально не путать объекты и методы.<br><br> Возможные способы доступа:<br><br> {2} - самый быстрый,&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp {3} , &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp {4}", 
	
	];

	protected $en = [
		0 => "",
		1 => "",
		2 => "",
	];

	public function __construct(int $num, array $m, $traceNumber = 0) {
		parent::__construct($num, $m, $traceNumber);
	}
}