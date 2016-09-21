<?php
$arr = [];
$this->traceResult = [];
$this->traceResult['log'] = [];
$this->traceResult['display'] = [];
$this->traceResult['inversion'] = [];
$argsCount = 0;
$k = 0;

for ($i = 0; $i < count($trace); ++$i) {

#--------------- file ---------------------------------------------------------
	$fileSep = '/';
	if (!array_key_exists('file', $trace[$i])) {
		$trace[$i]['file'] = '';
		$fileSep = '';
	}
	if ($k || $trace[$i]['file'] == $file) {
		++$k;

		$fileParts = explode('/', $trace[$i]['file']);

		$arr[$i]['file']
		=
		'<td class="trace_file">'.rtrim(array_pop($fileParts), '.php').'</td>';


			// path ...........................................................
		$arr[$i]['path']
		=
		'<td class="trace_path">'
		.str_replace(MICRO_DIR.'/', '', implode('/', $fileParts)).$fileSep.'</td>';

			// log-file .......................................................
		$this->traceResult['log'][$i]['file']
		=
		str_replace(MICRO_DIR.'/', '', $trace[$i]['file']);

#--------------- line ---------------------------------------------------------
		if (!array_key_exists('line', $trace[$i])) {
			$trace[$i]['line'] = '';
		}
		$arr[$i]['line']
		=
		'<td class="trace_line">'.$trace[$i]['line'].'</td>';

		$arr[$i]['func'] = '';

			// log-line .......................................................
		$this->traceResult['log'][$i]['line'] = '::'.$trace[$i]['line'].'::';

#--------------- class --------------------------------------------------------
		$classSep = '\\';
		if (!array_key_exists('class', $trace[$i])) {
			$trace[$i]['class'] = '';
			$classSep = '';
		}
		$classParts = explode('\\', $trace[$i]['class']);

		$arr[$i]['class'] = '<td class="trace_class">'.array_pop($classParts).'</td>';

			// log-class...................................................
		$this->traceResult['log'][$i]['class'] = $trace[$i]['class'];

			// nameSpace .....................................................
		$arr[$i]['nameSpace']
		=
		'<td class="trace_name_space">'.implode('\\', $classParts).$classSep.'</td>';


			// проверка инверсии FuncToLink ...................................
		$funcLink = '';
		if ($trace[$i]['class'] == get_class($this->R)
			&&
			$trace[$i]['function'] == '__callStatic')
		{
			if ($funcLink = $this->R->FuncToLink($trace[$i]['args'][0])) {

				$arr[$i]['func']
				=
				'<span class="trace_func"> => '.$funcLink.'</span>';
			}
		}
		else {
			$arr[$i]['func'] = '';
		}



#--------------- function -----------------------------------------------------
		if (!array_key_exists('function', $trace[$i])) {
			$trace[$i]['function'] = '';
		}

		$arr[$i]['function']
		=
		'<td class="trace_function">'.$trace[$i]['function']
		.$arr[$i]['func']
		.'</td>';

			// log-function....................................................
		$this->traceResult['log'][$i]['function'] = $trace[$i]['function'];

		if ($funcLink != '') {
			$this->traceResult['log'][$i]['function'] .= ' ----> {i} '.$funcLink;

			$this->traceResult['inversion'][$i] = &$this->traceResult['log'][$i];
		}


#--------------- args ---------------------------------------------------------
		$this->traceResult['log'][$i]['args'] = [];

		if (empty($trace[$i]['class']) || $trace[$i]['class'] != $ThisClass) {
			if (!array_key_exists('args', $trace[$i])) {
				$trace[$i]['args'] = [];
			}

			foreach ($trace[$i]['args'] as $Arg) {

					// object .................................................
				if (is_object($Arg)) {
					$objectParts = explode('\\', get_class($Arg));

					$obj = '<span class="trace_class">'.array_pop($objectParts).'</span>';

					$space = '<span class="trace_name_space">'
							 .implode('\\', $objectParts).'\\'.'</span>';

					$arr[$i]['args'][] = '<td class="trace_args">'.$space.$obj.'</td>';

						// log-args-object.....................................
					$this->traceResult['log'][$i]['args'][] = get_class($Arg);
				}
				
					// array ..................................................
				elseif (is_array($Arg)) {

					$arr[$i]['args'][] = '<td  class="trace_args array">[array]</td>';

						// log-args-array.....................................
					$this->traceResult['log'][$i]['args'][] = '[array]';
				}
				else {
					if (!empty($arr[$i]['func'])) {
						$arr[$i]['args'][] = '<td  class="trace_args trace_func">'.$Arg.'</td>';
					}
					else {
						$arr[$i]['args'][] = '<td  class="trace_args">'.$Arg.'</td>';
					}
						// log-args-остальные .................................
					$this->traceResult['log'][$i]['args'][] = $Arg;
				}
			}
			$cnt = count($trace[$i]['args']);
			$argsCount > $cnt
			?:
			$argsCount = $cnt;
		}
	}
}
$l = 1;
$this->traceResult['display'] = '';
$this->traceResult['display'] .= '<table class="micro_trace">';
foreach ($arr as  $ArrValue) {

	$this->traceResult['display'] .= '<tr class="color'.($l = $l*-1).'">'
					.$ArrValue['path']
					.$ArrValue['line']
					.$ArrValue['file']
					.$ArrValue['nameSpace']
					.$ArrValue['class']
					.$ArrValue['function'];

	if (!array_key_exists('args', $ArrValue)) {
		$ArrValue['args'] = [];
	}
	for ($k = 0; $k < $argsCount; ++$k) {
		if (array_key_exists($k, $ArrValue['args'])) {
			$this->traceResult['display'] .= $ArrValue['args'][$k];
		}
		else {
			$this->traceResult['display'] .= '<td class="trace_args"></td>';
		}
	}
}
$this->traceResult['display'] .= '</table>';