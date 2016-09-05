<?php
namespace MicroMir\Exception;

use MicroMir\Debug\Error\ErrorHandler;

class MicroException extends \Exception
{
	protected $mgs; 

	protected $exceptionCode = '+';

	public function __construct(int $num, array $m, $traceNumber) {
		$this->mgs = &$GLOBALS['MICROCODER_GLOBAL_SETTINGS'];
		$this->message = $this->prepareMessage($num, $m);
		$this->code = $this->exceptionCode." $num";
		ErrorHandler::instance()->microException($this, $traceNumber);
	}

	protected function prepareMessage(int $num, array $m) {
		if ($this->mgs['LOCALE'] == 'en') {
			$locale = 'en';
		} else {
			$locale = 'ru';
		}

		$mess = $this->$locale[$num];
		for ($i = 0; $i < count($m); $i++) {
			if ($this->mgs['DEVELOPMENT'] &&
            	array_key_exists($_SERVER['REMOTE_ADDR'], $this->mgs['DEVELOPMENT_IP']))
            {
				$m[$i] = $this->decor($m[$i]);
            }
			$mess = implode($m[$i], explode('{'.$i.'}', $mess));
		}
		return $mess;
	}

	protected function decor($param) {
		return "<span style=\" color: ffff89;\">".$param."</span>";
	}
}