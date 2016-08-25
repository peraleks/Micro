<?php
namespace Micro\Exception;

use Exception;
use Micro\Debug\Error\ErrorHandler;

class MicroException extends Exception
{
	protected $exceptionCode = '+';

	public function __construct(int $num, array $m, $traceNumber) {
		$this->message = $this->prepareMessage($num, $m);
		$this->code = $this->exceptionCode;
		ErrorHandler::instance()->microException($this, $traceNumber);
	}

	protected function prepareMessage(int $num, array $m) {
		if (defined('MICROCODER_LOCALE') && MICROCODER_LOCALE == 'en') {
			$locale = 'en';
		} else {
			$locale = 'ru';
		}

		$mess = $this->$locale[$num];
		for ($i = 0; $i < count($m); $i++) {
			$mess = implode($this->decor($m[$i]), explode('{'.$i.'}', $mess));
		}
		return $mess;
	}

	protected function decor($param) {
		return "<span style=\" color: ffff89; \">".$param."</span>";
	}
}