<?php
if (!defined ('in')) exit ();
$pag = (!empty ($_GET['archiv'])) ? $_GET['archiv'] : 1;



/*--- Výstup, DB works ---*/

$_META['keywords'] = $translate['archive.keys'];
$_META['description'] = $translate['archive.desc'];
$out = '<h1 align="center">' . $translate['links.archive'] . '</h1>'.n;
$title .= $sep . $translate['links.archive'];
$kolko = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_clanky WHERE `confirmed` = 1 AND showing = 1 AND added <= NOW()"));
$limit = $_CONFIG['list_arts'];
if ($kolko[0] > $limit) {
	$p = '<p align="center"><strong>' . $translate['paging'] . '</strong><br />'.n;
	for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) {
		$p .= '<a href="' . rwl('archiv', $i).'" title="' . langrep ('gopage', $i) . '">';
		$p .= ($i == $pag) ? '<b>['.$i.']</b></a>'.n : $i.'</a>'.n;};
	if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) {
		$p .= '<a href="' . rwl('archiv', $i).'" title="' . langrep ('gopage', $i) . '">';
		$p .= ($i == $pag) ? '<b>['.$i.']</b></a>'.n : $i.'</a>'.n;};
	$p .= '</p>'.n;
};



/*--- Prepočet strany ---*/

@$out .= $p;
$limit2 = ($pag - 1) * $limit;
$out .= HcmParser ("[hcm]arts,,{$limit2}ciarka{$limit}[/hcm]");
@$out .= $p;
if ($kolko[0] == 0) $out .= "<p>{$translate['nocontent']}</p>";