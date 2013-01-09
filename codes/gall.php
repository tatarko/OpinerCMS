<?php
if (!defined('in')) exit();



/*--- Práca s DB ---*/

$_META['keywords'] = $translate['gallery.keys'];
$_META['description'] = $translate['gallery.desc'];
$title .= $sep . $translate['links.gallery'];
$out = '<h1 align="center">' . $translate['albums.list'] . '</h1>'.n;
$kolko = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_gall WHERE showing = 1"));
$limit = $_CONFIG['list_cats'];
if ($kolko[0]>$limit) {
	$p = '<p align="center">'.n;
	for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) $p .= '<a href="' . rwl ('gallery', $i) . '" title="' . langrep ('gopage', $i) . '">[' . $i . ']</a>' . n;
	if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) $p .= '<a href="' . rwl ('gallery', $i) . '" title="' . langrep ('gopage', $i) . '">[' . $i . ']</a>' . n;
	$p .= '</p>' . n;
} else $p = '';



/*--- Generovanie výstupu ---*/

$out .= $p;
$pag = (isset ($_GET['gallery']) and $_GET['gallery'] != '') ? $_GET["gallery"] : 1;
if ($pag != 1) $title .= $sep . langrep ($translate['page'], $pag);
$limit2 = ($pag - 1) * $limit;
if ($kolko[0] > 0) $out .= HcmParser ('[hcm]gall_cats,' . $limit2 . '(ciarka)' . $limit . '[/hcm]');
else $out .= "<p>{$translate['er8']}</p>";
$out .= $p;
?>