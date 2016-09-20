<?php
$arr = [];
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


#--------------- line ---------------------------------------------------------
		if (!array_key_exists('line', $trace[$i])) {
			$trace[$i]['line'] = '';
		}
		$arr[$i]['line']
		=
		'<td class="trace_line">'.$trace[$i]['line'].'</td>';

		$arr[$i]['func'] = '';


#--------------- class --------------------------------------------------------
		$classSep = '\\';
		if (!array_key_exists('class', $trace[$i])) {
			$trace[$i]['class'] = '';
			$classSep = '';
		}
		$classParts = explode('\\', $trace[$i]['class']);

		$arr[$i]['class'] = '<td class="trace_class">'.array_pop($classParts).'</td>';


			// nameSpace .....................................................
		$arr[$i]['nameSpace']
		=
		'<td class="trace_name_space">'.implode('\\', $classParts).$classSep.'</td>';


			// проверка инверсии FuncToLink ...................................
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


#--------------- args ---------------------------------------------------------
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
				}
				
					// array ..................................................
				elseif (is_array($Arg)) {

					$arr[$i]['args'][] = '<td  class="trace_args array">[array]</td>';
				}
				else {
					if (!empty($arr[$i]['func'])) {
						$arr[$i]['args'][] = '<td  class="trace_args trace_func">'.$Arg.'</td>';
					}
					else {
						$arr[$i]['args'][] = '<td  class="trace_args">'.$Arg.'</td>';
					}
				}
			}
			$cnt = count($trace[$i]['args']);
			$argsCount > $cnt
			?:
			$argsCount = $cnt;
		}
		 // очистка последнего вызова от лишней информации
		if ($trace[$i]['class'] == $ThisClass) {
			  $arr[$i]['class']     = '<td></td>';
			  $arr[$i]['function']  = '<td></td>';
			  $arr[$i]['nameSpace'] = '<td></td>';
		}
	}
}
$l = 1;
$traceResult = '';
$traceResult .= '<table class="micro_trace">';
foreach ($arr as  $ArrValue) {

	$traceResult .= '<tr class="color'.($l = $l*-1).'">'
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
			$traceResult .= $ArrValue['args'][$k];
		}
		else {
			$traceResult .= '<td class="trace_args"></td>';
		}
	}
}
$traceResult .= '</table>';
