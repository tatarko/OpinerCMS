<?php


// Startup Config
$width = 700;
$height = 300;
$mainColor = array (127, 127, 127);
$hitsColor = array (145, 181, 255);
$vissColor = array (98, 130, 196);
$uipsColor = array (58, 91, 158);



// Generating Values
session_start();
if (!@file_exists ('../../media/get-config.php') or !isset ($_SESSION['day'])) die ('nepripojene');
include ('../../media/get-config.php');

$query = @mysql_query ("SELECT DISTINCT DATE_FORMAT(`kedy`, '%Y-%m') FROM `{$prefix}_hits` WHERE DATE_FORMAT(`kedy`, '%Y') = '{$_SESSION['day']}'");
while ($tab = @mysql_fetch_row ($query)) {
	$kolko = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*), COUNT(DISTINCT ip) FROM {$prefix}_hits WHERE DATE_FORMAT(kedy,'%Y-%m') = '$tab[0]'"));
	$kolk2 = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_visits WHERE DATE_FORMAT(kedy,'%Y-%m') = '$tab[0]'"));
	$array[] = array (
		'day'		=> substr ($tab[0], 5, 2),
		'hits'		=> $kolko[0],
		'visits'	=> $kolk2[0],
		'uip'		=> $kolko[1],
	);
};
if (isset ($array)) {
	$max = 0;
	foreach ($array as $value) {
		if ($value['hits'] > $max) $max = $value['hits'];
	};
} else die ('žiadné výsledy'); 



// Image
header ('Content-type: image/png');
$im = @imagecreatetruecolor ($width, $height) or die ();
imagefill ($im, 0, 0, imagecolorallocate ($im, 255, 255, 255));
define ('gColor', imagecolorallocate ($im, $mainColor[0], $mainColor[1], $mainColor[2]));
define ('hCol', imagecolorallocate ($im, $hitsColor[0], $hitsColor[1], $hitsColor[2]));
define ('vCol', imagecolorallocate ($im, $vissColor[0], $vissColor[1], $vissColor[2]));
define ('uCol', imagecolorallocate ($im, $uipsColor[0], $uipsColor[1], $uipsColor[2]));
imagerectangle ($im, 0, 0, $width - 1, $height - 1, gColor);



// Grafové hranice
$left = strlen ($max) * 7 + 14;
imagerectangle ($im, $left, 10, $width - 11, $height - 46, gColor);
for ($i = 0; $i <= 1; $i += 0.1) {
	$vyska = 10 + round (($height - 56) * $i);
	$actn = round ($max - $max * $i);
	$pos = round (($left - strlen ($actn) * 7) / 2);
	imageline ($im, $left, $vyska, $width - 11, $vyska, gColor);
	imagestring ($im, 4, $pos, $vyska - 7, $actn, gColor);
};


// Hodnoty do grafu
$inWidth = $width - $left - 11;
$inHeight = $height - 56;
$kolko = count ($array);
$stl = floor ($inWidth / $kolko);
$i = 0;
foreach ($array as $value) {
	$vyskaH = round ($inHeight * ($value['hits'] / $max));
	$vyskaV = round ($inHeight * ($value['visits'] / $max));
	$vyskaU = round ($inHeight * ($value['uip'] / $max));
	$posS = ($stl * $i + $left) + (($stl - 14) / 2);
	imagefilledrectangle ($im, $stl * $i + $left + 2, $inHeight - $vyskaH + 10, $left + $stl * ++$i - 2, $inHeight + 10, hCol);
	imagefilledrectangle ($im, $stl * ($i - 1) + $left + 5, $inHeight - $vyskaV + 10, $left + $stl * $i - 5, $inHeight + 10, vCol);
	imagefilledrectangle ($im, $stl * ($i - 1) + $left + 7, $inHeight - $vyskaU + 10, $left + $stl * $i - 7, $inHeight + 10, uCol);
	imagestring ($im, 4, $posS, $height - 40, $value['day'], gColor);
	imagestring ($im, 1, $posS, $height - 25, $value['hits'], hCol);
	imagestring ($im, 1, $posS, $height - 18, $value['visits'], vCol);
	imagestring ($im, 1, $posS, $height - 11, $value['uip'], uCol);
};



// Out
imagerectangle ($im, $left, 10, $width - 11, $height - 46, gColor);
imagepng ($im);
imagedestroy ($im);
?>