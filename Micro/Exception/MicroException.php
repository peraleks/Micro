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

	public function __construct(int $num, array $replace, $traceNumber) {
		$this->message = $this->prepareMessage($num, $replace);
		$this->code = $this->exceptionCode." $num";
		ErrorHandler::instance()->microException($this, $traceNumber);
	}

	protected function prepareMessage(int $num, array $replace) {
		if (defined('MICRO_LOCALE') && MICRO_LOCALE == 'en') {
			$locale = 'en';
		} else {
			$locale = 'ru';
		}
		$logError = $this->$locale[$num];

		foreach ($this->css as $CssKey => $CssValue) {
			foreach ($replace as $ReplaceKey => $ReplaceValue) {

	            $logError = str_replace($CssKey.$ReplaceKey.$CssValue, $ReplaceValue, $logError);
			}
		}
		$result['logError'] = $logError;

		if (defined('MICRO_DEVELOPMENT') && MICRO_DEVELOPMENT === true) {

			$displayError = $this->$locale[$num];

			foreach ($this->css as $CssKey => $CssValue) {
				foreach ($replace as $ReplaceKey => $ReplaceValue) {

					$displayError = str_replace($CssKey.$ReplaceKey.$CssValue,
										$this->decor($ReplaceValue, $CssKey),
										$displayError);
				}
			}
			$result['displayError'] = $displayError;
        }
		return $result;
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