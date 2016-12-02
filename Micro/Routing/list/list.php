<?php

$td_optional = '<td class="back-parts optional">';

$td_params   = '<td class="back-parts params">';

$td_text     = '<td class="back-parts text">';




$partsCount = 0;
$routeMask  = '';

// добавляем 404 страницу в массив $routes
$this->routes[''] = [];
$this->routes['']['path'] = '';
$this->routes['']['name'] = '404';

isset($this->match404['file'])
    ? $this->routes['']['file'] = $this->match404['file']
    : $this->routes['']['file'] = '';

isset($this->match404['action'])
    ? $this->routes['']['verbs']['get']['action'] = $this->match404['action']
    : $this->routes['']['verbs']['get']['action'] = '';

if (isset($this->match404['controller'])) {
    $this->routes['']['verbs']['get']['controller']
        = $this->routes['']['controller']
        = $this->match404['controller'];
} else {
    $this->routes['']['verbs']['get']['controller'] = 'MicroCoder_default';
}

// готовим все маршруты для таблицы и сохраняем в массив
foreach ($this->routes as $RoutesKey => $RoutesValue) {
    unset($splitParts);

    if (isset($RoutesValue['parts'])) {
        $routeMask = '/'.implode('/', $RoutesValue['parts']);

        isset($RoutesValue['optional'])
            ? $optional = $RoutesValue['optional']
            : $optional = 999;

        $routeParts = explode('/', ltrim($RoutesValue['path'], '/'));

        // оборачиваем в теги части маршрутов parts для parts-раздела
        // (выделяем необязательные и обычные параметры)
        for ($i = 0; $i < count($RoutesValue['parts']); $i++) {

            if ($i >= $optional) {
                $tag = $td_optional;
            } elseif (preg_match('/^{/', $routeParts [$i])) {
                $tag = $td_params;
            } else {
                $tag = $td_text;
            }
            $splitParts[$i] = $tag.$RoutesValue['parts'][$i].'</td>';
        }
    } else {
        // разбиваем для parts-раздела и оборачиваем в теги простые адреса без параметров
        if ($RoutesValue['path'] == '/') {
            $splitParts[0] = $td_text.'/</td>';
        } else {
            $arr = explode('/', ltrim($RoutesValue['path'], '/'));
            foreach ($arr as $val) {
                $splitParts[] = $td_text.$val.'</td>';
            }
        }
        $routeMask = $RoutesValue['path'];
    }

    // добавляем в общий массив
    $listArr[$RoutesKey]['routeMask'] = $routeMask;
    $listArr[$RoutesKey]['splitParts'] = $splitParts;

    // считаем количество ячеек таблицы для parts-раздела по самому длинному маршруту
    $cnt = count($splitParts);
    $partsCount > $cnt ?: $partsCount = $cnt;

    // имена маршрутов
    if (array_key_exists('nSpace', $RoutesValue)) {
        $listArr[$RoutesKey]['name'] = $RoutesValue['nSpace'].'/';
    } else {
        $listArr[$RoutesKey]['name'] = '';
    }
    if (isset($RoutesValue['name'])) {
        $listArr[$RoutesKey]['name'] .= $RoutesValue['name'];
    } else {
        $listArr[$RoutesKey]['name'] = '';
    }

    // файлы маршрутов
    if (isset($RoutesValue['file'])) {
        $listArr[$RoutesKey]['file'] = $RoutesValue['file'];
    } else {
        $listArr[$RoutesKey]['file'] = '';
    }

    // имена контроллеров
	$controller = '';
    $different = false;
//    \d::p($RoutesValue);
    foreach($RoutesValue['verbs'] as $verbsKey => $verbsValue) {
	    $next = $verbsValue['controller'];
	    if ($controller == '') {
            $controller = $next;
            continue;
        }
        if ($controller == $next) continue;
	    strcmp($controller, $next) < 0
	        ? $count = strlen($controller)
            : $count = strlen($next);

        $delimiter = 0;
	    for ($i = 0; $i < $count; ++$i) {
	        if (($controller[$i] == "\\") && $next[$i] == "\\") {
	            $delimiter = $i + 1;
            }
	        if ($controller[$i] !== $next[$i]) {
                $different = true;
	            $controller = substr($controller, 0, $delimiter);
	            break;
            }
        }
//        \d::p($controller);
    }

    $listArr[$RoutesKey]['controller'] = $controller;
    !$different ?: $listArr[$RoutesKey]['controller'] .= ' =>>';

    // добавляем  method action controller
    foreach ($this->methods as $methodsKey => $value) {
        $listArr[$RoutesKey]['mac'][] = [];
        $k = count($listArr[$RoutesKey]['mac']) - 1;

        if (isset($RoutesValue['verbs'][$methodsKey])) {
            $listArr[$RoutesKey]['mac'][$k][] = $methodsKey;
            $listArr[$RoutesKey]['mac'][$k][] = $RoutesValue['verbs'][$methodsKey]['action'];

            $listArr[$RoutesKey]['mac'][$k][]
                = str_replace($controller, '', $RoutesValue['verbs'][$methodsKey]['controller']);
//
//            $RoutesValue['verbs'][$methodsKey]['controller'] == $RoutesValue['controller']
//                ?  ''
//                : $listArr[$RoutesKey]['mac'][$k][] = $RoutesValue[$methodsKey]['controller'];
        } else {
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
			<div class="narrow" data-cell="0">
				&#10040;
			</div>
			<input id="input_file">
		</th>
		<th></th>
		<th>
			<div class="narrow" data-cell="2">
				&#10040;
			</div>
			<input id="input_name">
		</th>
		<th colspan="<?= $partsCount + 1 ?>" class="back-parts">
			<input id="input_parts">
		</th>
		<th>
			<div class="narrow" data-cell="<?= $partsCount + 4 ?>">
				&#10040;
			</div>
			<input id="input_route">
		</th>
		<th>
			<div class="narrow" data-cell="<?= $partsCount + 5 ?>">
				&#10040;
			</div>
			<input id="input_controller">
		</th>
		<?php
		$i = 0;
		foreach ($this->methods as $methodsKey => $value): ?>
			<th>
				<div class="but_method" data-cell="
					<?= $methodsKey ?>
				">
					<?= $methodsKey ?>
				</div>
			</th>
			<th>
				<div class="narrow" data-cell="<?= ($partsCount + 7 + ($i * 3)) ?>">
					&#10040;
				</div>
				<input id="input_method">
			</th>
			<th>
				<div class="narrow" data-cell="<?= ($partsCount + 8 + ($i * 3)) ?>">
					&#10040;
				</div>
				<input id="input_method">
			</th>
		<?php
		++$i;
		endforeach;
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
	foreach($listArr as $ListArrKey => $ListArrValue): ?>
		<tr>
		<td class="file"><?= 			$ListArrValue['file'] ?></td>
		<td><?= 						++$counter			  ?></td>
		<td class="name"><?= 			$ListArrValue['name'] ?></td>
		<td class="back-parts join"><?= $ListArrValue['routeMask'] ?></td>
		<?php
		for ($i = 0; $i < $partsCount; $i++) {

			if (array_key_exists($i, $ListArrValue['splitParts'])) {
				echo $ListArrValue['splitParts'][$i];
			}
			else {
				echo '<td class="back-parts"></td>';
			}
		}
		?>
		<td class="route"><?= $ListArrKey ?></td>
		<td class="controller"><?= $ListArrValue['controller'] ?></td>
		<?php
		foreach ($ListArrValue['mac'] as  $MacValue): ?>

			<td class="method"><?= $MacValue[0] ?></td>
			<td class="action"><?= $MacValue[1] ?></td>
			<td class="method_controller"><?= $MacValue[2] ?></td>
		<?php
		endforeach; ?>
		</tr>
	<?php
	endforeach; ?>
</table>
</div>
</body>
<script>
	<?php echo file_get_contents(__DIR__.'/list.js'); ?>
</script>
</html>