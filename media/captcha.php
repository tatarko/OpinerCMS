<?php


// Generating Values
session_start();
$text = mt_rand (10000, 99999);
$_SESSION['as'] = sha1 ($text);

// Image
header ('Content-type: image/png');
$im = @imagecreatetruecolor (70, 30) or die ();
imagefill ($im, 0, 0, imagecolorallocate ($im, 255, 255, 255));


// Dots
for ($y = 5; $y < 30; $y += 10) {
	for ($x = 5; $x < 70; $x += 10) {
		$r = mt_rand (6, 8);
		$col = $r*30;
		if (!isset ($color[$col])) $color[$col] = imagecolorallocate ($im, $col, $col, $col);
		imagefilledellipse ($im, $x, $y, $r, $r, $color[$col]);
	};
};
imagestring ($im, 5, 12, 7, $text, 0);
imagepng ($im);
imagedestroy ($im);
?>