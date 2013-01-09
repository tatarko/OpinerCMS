<?php
if (!defined('in')) exit ();

$query = explode ('-', $_GET['galeria']);
$id = $query[0];
if (false !== ($index = array_search ('komentare', $query))) unset ($query[$index], $query[($index + 1)]);
$pag = (false !== ($pos = array_search ('strana', $query)) and $pos == (count ($query) - 2)) ? ($query[(count ($query) - 1)]) : 1;



/*--- Hlavný výstup ---*/

if ($info = @mysql_fetch_row (@mysql_query("SELECT skr, nadpis, popis,autor FROM {$prefix}_gall WHERE id = '$id' LIMIT 1"))) {
	$_META['keywords'] = str_replace ('-', ', ', $info[0]);
	$_META['description'] = $info[2];
	$title .= $sep . $info[1];
	if ($pag != 1) $title .= $sep . langrep ('page', $pag);
	$out = '<h1 class="OpinerGalleryHead">' . $info[1] . '</h1>' . n;
	if ($info[2] != '') $out .= '<p>' . $info[2] . '<p>' . n;
	$kolko = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_img WHERE cat = '$id'"));
	$limit = $_CONFIG['list_imgs'];



	/*--- Stránkovanie ---*/
	
	if ($kolko[0] > $limit) {
		$p = '<p align="center">' . n;
		for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) {
			$p .= '<a href="'.rwl ('galeria', $id.'-'.$info[0].'-strana-'.$i).'" title="' . langrep ('gopage', $i) . '">';
			$p .= ($i == $pag) ? '<b>['.$i.']</b></a>'.n : '<b>'.$i.'</b></a>'.n;};
		if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) {
			$p .= '<a href="'.rwl ('galeria', $id.'-'.$info[0].'-strana-'.$i).'" title="' . langrep ('gopage', $i) . '">';
			$p .= ($i == $pag) ? '<b>['.$i.']</b></a>'.n : '<b>'.$i.'</b></a>'.n;};
		$p .= '</p>' . n;
	} else $p = '';



	/*--- Výstup ---*/

	$limit2 = ($pag - 1) * $limit;
	$out .= HcmParser ('[hcm]gall_imgs,' . $id . '[/hcm]');
	$out .= $p;
	$out .= HcmParser ('[hcm]com,cat_'.$id.'[/hcm]');
} else {
	$out = "<p>{$translate['er9']}</p>";
	header("HTTP/1.0 404 Not Found");
};