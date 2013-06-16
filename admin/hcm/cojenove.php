<?php

if(!function_exists('json_decode') || !function_exists('file_get_contents')) {
	
	return '';
}

$jsonData = json_decode(file_get_contents('http://cojenove.sk/api.php?app=' . urlencode($_SERVER['SERVER_NAME']) . (isset($param2) && (int)$param2 ? '&limit=' . (int)$param2 : '')), true);

if(!isset($jsonData['articles'])) {
	
	return '';
}

$isMenu = isset($param1) && $param1 == 'menu';
$out = $isMenu ? '<ul>' . PHP_EOL : '';

foreach($jsonData['articles'] as $data) {

	if($isMenu)
	$out .= '<li><a href="' . $data['url'] . '">' . htmlspecialchars($data['title']) . '</a></li>' . PHP_EOL;
	else $out .= '<h5><a href="' . $data['url'] . '">' . htmlspecialchars($data['title']) . '</a></h5>
	<p>' . htmlspecialchars($data['content']) . ' <em><small>' . date('d.m.Y @H:i', $data['time']) . '</small></em></p>' . PHP_EOL;
}
$out .= $isMenu ? ' <li><small><a href="http://cojenove.sk/">&raquo; Viac na Čojenové.sk</a></small></li>' . PHP_EOL : '<p><small><a href="http://cojenove.sk/">&raquo; Viac na Čojenové.sk</a></small></p>' . PHP_EOL;
$out .= $isMenu ? '</ul>' . PHP_EOL : '';

return $out;
?>