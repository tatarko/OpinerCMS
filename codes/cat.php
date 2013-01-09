<?php
if (!defined ('in')) exit ();
$query = explode ('-', $_GET['kategoria']);
$id = adjust ($query[0], true);
$pag = (false !== ($pos = array_search ('strana', $query)) and $pos == (count ($query) - 2)) ? ($query[(count ($query) - 1)]) : 1;



/*--- Výstup, DB works ---*/

if ($info = @mysql_fetch_row (@mysql_query ("SELECT `nadpis`, `popis`, `id`, `skr` FROM `{$prefix}_cats` WHERE `id` = $id AND `showing` = 1 LIMIT 1"))) {
	$_META['keywords'] = str_replace ('-', ', ', $info[3]);
	$_META['description'] = $info[1];
	$out = '<h1 align="center">' . $info[0] . '</h1>
<blockquote><p>' . $info[1] . '</p></blockquote>'.n;
	$title .= $sep . $info[0];
	$kolko = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_clanky WHERE (cat = '$id' OR cat2 = '$id' OR cat3 = '$id') AND showing = 1 AND `confirmed` = 1 AND `added` <= NOW()"));
	$limit = $_CONFIG['list_arts'];
	if ($kolko[0] > $limit) {
		$p = '<p align="center"><strong>' . $translate['paging'] . '</strong><br />'.n;
		for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) {
			$p .= '<a href="' . rwl('kategoria', $id.'-'.$info[3].'-strana-'.$i).'" title="' . langrep ('gopage', $i) . '">';
			$p .= ($i == $pag) ? '<b>['.$i.']</b></a>'.n : $i.'</a>'.n;};
		if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) {
			$p .= '<a href="' . rwl('kategoria', $id.'-'.$info[3].'-strana-'.$i).'" title="' . langrep ('gopage', $i) . '">';
			$p .= ($i == $pag) ? '<b>['.$i.']</b></a>'.n : $i.'</a>'.n;};
		$p .= '</p>'.n;
	};



	/*--- Prepočet strany ---*/

	@$out .= $p;
	$limit2 = ($pag - 1) * $limit;
	$out .= HcmParser ("[hcm]arts, $id, {$limit2}ciarka{$limit}[/hcm]");
	@$out .= $p;
	if ($kolko[0] == 0) $out .= "<p>{$translate['nocontent']}</p>";
} else {
	header("HTTP/1.0 404 Not Found");
	$out = "<p>{$translate['wrongreq']}</p>";
};