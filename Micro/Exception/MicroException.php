<?php
namespace MicroMir\Exception;

use MicroMir\Debug\Error\ErrorHandler;

class MicroException extends \Exception
{
	protected $exceptionCode = '+';

	protected $css = [
						'{' => '}',
						'#' => '#',
					 ];

	public function __construct(int $num, array $m, $traceNumber) {
		$this->message = $this->prepareMessage($num, $m);
		$this->code = $this->exceptionCode." $num";
		ErrorHandler::instance()->microException($this, $traceNumber);
	}

	protected function prepareMessage(int $num, array $m) {
		if (defined('MICRO_LOCALE') && MICRO_LOCALE == 'en') {
			$locale = 'en';
		} else {
			$locale = 'ru';
		}
		$mess = $this->$locale[$num];

		foreach ($this->css as $CssKey => $CssValue) {
			for ($i = 0; $i < count($m); $i++) {
				if (defined('MICRO_DEVELOPMENT') && MICRO_DEVELOPMENT === true) {
					$mDec = $this->decor($m[$i], $CssKey);
	            }
				$mess = str_replace($CssKey.$i.$CssValue, $mDec, $mess);
			}
		}
		return $mess;
	}

	protected function decor($param, $CssKey)
	{
		switch ($CssKey) {
			case '{':
				return
				"<span class=\"warning\">".$param."</span>";
				break;
			
			case '#':
				return
				"<span class=\"error\">".$param."</span>";
				break;
			
			default:
				return $param;
				break;
		}
	}
}