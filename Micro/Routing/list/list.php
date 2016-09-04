<?php

$td_optional = '<td class="back-parts optional">';

$td_params   = '<td class="back-parts params">';

$td_text     = '<td class="back-parts text">';




$partsCount = 0;
$joinParts  = '';

foreach ($this->routes as $RoutesKey => $RoutesValue) {

	unset($splitParts);

	if (array_key_exists('parts', $RoutesValue)) {

		$joinParts = implode(' ', $RoutesValue['parts']); 

		array_key_exists('optional', $RoutesValue)
		?
		$optional = $RoutesValue['optional']
		:
		$optional = 999;

		$routeParts = explode('/', ltrim($RoutesValue['route'], '/'));

        	// оборачиваем в теги части маршрутов parts для parts-раздела
        	// (выделяем необязательные и обычные параметры)
		for ($i = 0; $i < count($RoutesValue['parts']); $i++) {

			if ($i >= $optional) {
				$tag = $td_optional;
			}
			elseif (preg_match('/^{/', $routeParts [$i])) {
				$tag = $td_params;
			}
			else {
				$tag = $td_text;
			}
			$splitParts[$i] = $tag.$RoutesValue['parts'][$i].'</td>';
		}
	}
	else {
        	// разбиваем для parts-раздела и оборачиваем в теги простые адреса без параметров 
		if ($RoutesValue['route'] == '/') {
			$splitParts[0] = $td_text.'/</td>';
			$joinParts = '/';
		}
		else {
			$arr = explode('/', ltrim($RoutesValue['route'], '/'));
			$joinParts = implode('', $arr);
			foreach ($arr as $val) {
				$splitParts[] = $td_text.$val.'</td>';
			}
		}
	}

    	// добавляем в общий массив 
	$listArr[$RoutesKey]['joinParts']  = $joinParts;
	$listArr[$RoutesKey]['splitParts'] = $splitParts;

    	// считаем количество ячеек таблицы для parts-раздела по самому длинному маршруту
	$cnt = count($splitParts);
	$partsCount > $cnt
	?:
	$partsCount = $cnt;

    	// имена маршрутов
	if (array_key_exists('name', $RoutesValue)) {
		$listArr[$RoutesKey]['name'] = $RoutesValue['name'];
	}
	else {
		$listArr[$RoutesKey]['name'] = '';
	}

        // файлы маршрутов
	if (array_key_exists('file', $RoutesValue)) {
		$listArr[$RoutesKey]['file'] = $RoutesValue['file'];
	}
	else {
		$listArr[$RoutesKey]['file'] = '';
	}

		// имена контроллеров
	if (array_key_exists('controller', $RoutesValue)) {
		$listArr[$RoutesKey]['controller'] = $RoutesValue['controller'];
	}
	else {
		$listArr[$RoutesKey]['controller'] = '';
	}

		// добавляем  method action controller
	foreach ($this->methods as $MethodsValue) {
				   $listArr[$RoutesKey]['mac'][] = [];
		$k = count($listArr[$RoutesKey]['mac']) - 1;

		if (array_key_exists($MethodsValue, $RoutesValue)) {

			$listArr[$RoutesKey]['mac'][$k][] = $MethodsValue;
			$listArr[$RoutesKey]['mac'][$k][] = $RoutesValue[$MethodsValue]['action'];

			$RoutesValue[$MethodsValue]['controller'] = $RoutesValue['controller']
			?
			$listArr[$RoutesKey]['mac'][$k][] = ''
			:
			$listArr[$RoutesKey]['mac'][$k][] = $RoutesValue[$MethodsValue]['controller'];
		}
		else {
			$listArr[$RoutesKey]['mac'][$k] = ['', '', ''];
		}
	}
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>MicroCoder routes list</title>
    <style type="text/css">
    	<?php echo file_get_contents(__DIR__.'/list.css'); ?>
    </style>
</head>
<body>
<div id='wrap'>
<table id="table" border="5">
	<tr>
		<th>
			<input type="checkbox" class="but" data-cell="0">
			<div></div>
			<input id="input_file">
		</th>
		<th></th>
		<th>
			<input type="checkbox" class="but" data-cell="2">
			<div></div>
			<input id="input_name">
		</th>
		<th colspan="<?= $partsCount + 1 ?>" class="back-parts">
			<div></div>
			<input id="input_parts">
		</th>
		<th>
			<input type="checkbox" class="but" data-cell="<?= $partsCount + 4 ?>">
			<div></div>
			<input id="input_route">
		</th>
		<th>
			<input type="checkbox" class="but" data-cell="<?= $partsCount + 5 ?>">
			<div></div>
			<input id="input_controller">
		</th>
		<?php
		for ($i = 0; $i < count($this->methods); $i++) {
			echo
			'<th>
				<div type="checkbox" class="but_method" data-cell="'
					.$this->methods[$i].
				'">'
					.$this->methods[$i].
				'</div>
			</th>';
			echo
			'<th>
				<input type="checkbox" class="but" data-cell="'
					.($partsCount + 7 + ($i * 3)).
				'">
				<div></div>
				<input id="input_method">
			</th>';
			echo
			'<th>
				<input type="checkbox" class="but but_controller" data-cell="'
				.($partsCount + 8 + ($i * 3)).
				'">
				<div></div>
				<input id="input_method">
			</th>';
		}
		?>
	</tr>
	<tr id="sort">
		<th></th>
		<th class="sort_pointer">&#8593</th>
		<th></th>
		<th></th>
		<?php for ($i = 0; $i < ($partsCount + count($this->methods) * 3); $i++)
				  { echo "<th></th>"; }
		?>
		<th></th>
		<th></th>
	</tr>
	<?php
	$counter = 0;
	foreach($listArr as $ListArrKey => $ListArrValue) {
		$counter++;
		echo '<tr>';
		echo '<td class="file">'		   .$ListArrValue['file']	  .'</td>';
		echo "<td>$counter</td>";
		echo '<td class="name">'		   .$ListArrValue['name']	  .'</td>';
		echo '<td class="back-parts join">'.$ListArrValue['joinParts'].'</td>';

		for ($i = 0; $i < $partsCount; $i++) {

			if (array_key_exists($i, $ListArrValue['splitParts'])) {
				echo $ListArrValue['splitParts'][$i];
			}
			else {
				echo '<td class="back-parts"></td>';
			}
		}
		echo '<td class="route">'.$ListArrKey.'</td>';
		echo '<td class="controller">'.$ListArrValue['controller'].'</td>';

		foreach ($ListArrValue['mac'] as  $MacValue) {

			echo '<td class="method">'			 .$MacValue[0].'</td>';
			echo '<td class="action">'			 .$MacValue[1].'</td>';
			echo '<td class="method_controller">'.$MacValue[2].'</td>';
	
		}
		echo '</tr>';
	}
	?>
</table>
</div>
</body>
<script>
	<?php echo file_get_contents(__DIR__.'/list.js'); ?>
</script>
</html>