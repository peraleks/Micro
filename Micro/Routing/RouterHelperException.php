<?php
namespace MicroMir\Routing;

use MicroMir\Exception\MicroException;

class RouterHelperException extends MicroException
{
	protected $exceptionCode = 'Root';

	protected $ru = [
		0 => "Mетод {0} проигнорирован. Т.к. не найден в {1}",
	
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