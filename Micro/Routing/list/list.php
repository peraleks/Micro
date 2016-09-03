<?php

$td_optional = '<td class="back-parts optional">';

$td_params   = '<td class="back-parts params">';

$td_text     = '<td class="back-parts text">';




$partsCount = 0;
$joinParts = '';

foreach ($this->routes as $thisRoutesKey => $thisRoutesValue) {

	unset($splitParts);

	if (array_key_exists('parts', $thisRoutesValue)) {

		$joinParts = implode(' ', $thisRoutesValue['parts']); 

		array_key_exists('optional', $thisRoutesValue)
		?
		$optional = $thisRoutesValue['optional']
		:
		$optional = 999;

		$routeParts = explode('/', ltrim($thisRoutesValue['route'], '/'));

        	// оборачиваем в теги части маршрутов parts для parts-раздела
        	// (выделяем необязательные и обычные параметры)
		for ($i = 0; $i < count($thisRoutesValue['parts']); $i++) {

			if ($i >= $optional) {
				$tag = $td_optional;
			}
			elseif (preg_match('/^{/', $routeParts [$i])) {
				$tag = $td_params;
			}
			else {
				$tag = $td_text;
			}
			$splitParts[$i] = $tag.$thisRoutesValue['parts'][$i].'</td>';
		}
	}
	else {
        	// разбиваем для parts-раздела и оборачиваем в теги простые адреса без параметров 
		if ($thisRoutesValue['route'] == '/') {
			$splitParts[0] = $td_text.'/</td>';
			$joinParts = '/';
		}
		else {
			$arr = explode('/', ltrim($thisRoutesValue['route'], '/'));
			$joinParts = implode('', $arr);
			foreach ($arr as $val) {
				$splitParts[] = $td_text.$val.'</td>';
			}
		}
	}

    	// добавляем в общий массив 
	$routes[$thisRoutesKey]['joinParts']  = $joinParts;
	$routes[$thisRoutesKey]['splitParts'] = $splitParts;

    	// считаем количество ячеек таблицы для parts-раздела по самому длинному маршруту
	$cnt = count($splitParts);
	$partsCount > $cnt
	?:
	$partsCount = $cnt;

    	// имена маршрутов
	if (array_key_exists('name', $thisRoutesValue)) {
		$routes[$thisRoutesKey]['name'] = $thisRoutesValue['name'];
	}
	else {
		$routes[$thisRoutesKey]['name'] = '';
	}

        // файлы маршрутов
	if (array_key_exists('file', $thisRoutesValue)) {
		$routes[$thisRoutesKey]['file'] = $thisRoutesValue['file'];
	}
	else {
		$routes[$thisRoutesKey]['file'] = '';
	}

	// имена контроллеров
	if (array_key_exists('controller', $thisRoutesValue)) {
		$routes[$thisRoutesKey]['controller'] = $thisRoutesValue['controller'];
	}
	else {
		$routes[$thisRoutesKey]['controller'] = '';
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
		<th colspan="<?= $partsCount + 1 ?>"  class="back-parts">
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
	</tr>
	<tr id="sort">
		<th></th>
		<th class="sort_pointer">&#8593</th>
		<th></th>
		<th></th>
		<?php for ($i = 0; $i < $partsCount; $i++) { echo "<th></th>"; } ?>
		<th></th>
		<th></th>
	</tr>
	<?php
	$counter = 0;
	foreach($routes as $key => $route) {
		$counter++;
		echo '<tr>';
		echo '<td class="file">'.$route['file'].'</td>';
		echo "<td>$counter</td>";
		echo '<td class="name">'.$route['name'].'</td>';
		echo '<td class="back-parts join">'.$route['joinParts'].'</td>';

		for ($i = 0; $i < $partsCount; $i++) {

			if (array_key_exists($i, $route['splitParts'])) {
				echo $route['splitParts'][$i];
			}
			else {
				echo '<td class="back-parts"></td>';
			}
		}
		echo '<td class="route">'.$key.'</td>';
		echo '<td class="controller">'.$route['controller'].'</td>';
		echo '</tr>';
	}
	?>
</table>
</body>
<script>
	<?php echo file_get_contents(__DIR__.'/list.js'); ?>
</script>
</html>