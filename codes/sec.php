<?php



/*--- SecInfo ---*/

$query = explode ('-', $_GET['sekcia']);
$id = $query[0] + 0;
$pag = (false !== ($pos = array_search ('komentare', $query)) and $pos == (count ($query) - 2)) ? ($query[(count ($query) - 1)]) : 1;



/*--- Out ---*/

if ($info = @mysql_fetch_row (@mysql_query("SELECT seo, nadpis, text, com, msec, popis FROM {$prefix}_sec WHERE id = '$id' LIMIT 1"))) {
	$title .= $sep . $info[1];
	$out = '<h1 align="center">' . $info[1] . '</h1>';
	if ($info[4] > 0) {
		if ($info2 = @mysql_fetch_row (@mysql_query ("SELECT seo, nadpis FROM {$prefix}_sec WHERE id = $info[4] LIMIT 1")))
		$secs[] = '	<li><a href="' . rwl('sekcia', $info[4].'-'.$info2[0]).'">&laquo;&laquo;&laquo; ' . langrep ('sections.upsec', $info2[1]) . '</a></li>';
	};
	$sql = @mysql_query ("SELECT id, seo, nadpis FROM {$prefix}_sec WHERE msec = $id ORDER BY id ASC");
	while ($value = @mysql_fetch_row ($sql))
	$secs = '	<li><a href="' . rwl ('sekcia', $value[0] . '-' . $value[1]) . '">' . langrep ('sections.downsec', $value[2]) . ' &raquo;&raquo;&raquo;</a></li>';
	if (isset ($secs)) $out .= "<ul>\n" . implode ("\n", $secs) . "\n</ul>\n"; 
	$texter = ($_CONFIG['wysiwyg'] == 'texyla') ? texyla (HcmParser ($info[2]), 'admin') : HcmParser ($info[2]);
	$out .= $texter;
	if ($info[3] == 1) $out .= HcmParser('[hcm]com,sec_' . $id . '[/hcm]');
	$texter = strip_tags($texter);
	$_META['keywords'] = str_replace ('-', ', ', $info[0]);
	if ($info[5] == '') {
		$_META['description'] = mb_strlen ($texter) > 96 ? mb_substr ($texter, 0, mb_strpos ($texter, ' ', 96)) . ' (...)' : $texter;
		$_META['description'] = preg_replace ('#[\s]+#', ' ', $_META['description']);
		if (substr ($_META['description'], 0, 1) == ' ') $_META['description'] = substr ($_META['description'], 1);
		trim ($_META['description']);
	} else $_META['description'] = setQ ($info[5]);
} else {
	$out = "<p>{$translate['nocontent']}</p>";
	header("HTTP/1.0 404 Not Found");
};
?>