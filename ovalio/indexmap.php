<?php



/*--- ReWriteLink ---*/
function rwl ($index, $value) {
	global $_CONFIG;
	$out = ($_CONFIG['rewrite'] == 1) ? "$index-$value.html" : "?$index=$value";
	return $out;
};



/*--- Prepare string to output ---*/
function im_format ($string) {
	$string = strip_tags ($string);
	$string = implode (' ', array_unique (array_filter (explode (' ', str_replace (array ("\n", "\r"), ' ', $string)))));
	return $string;
}



/*--- Main ---*/
define ('in', true);
include ('../media/get-config.php');
Header ('Content-type: text/xml');



/*--- MainOut ---*/
echo '<?xml version="1.0" encoding="UTF-8"?>
<indexmap>' . "\n";
$links[] = '	<item type="web" url=""></item>';



/*--- Kategórie ---*/
$query = mysql_query ("SELECT `skr`, `id` FROM `{$prefix}_cats` WHERE `showing` = 1");
while ($value = mysql_fetch_row ($query)) {
	$links[] = '	<item type="web" url="' . rwl ('kategoria', "$value[1]-$value[0]") . '"></item>';
	list ($count) = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM `{$prefix}_clanky` WHERE (`cat` = $value[1] OR `cat2` = $value[1] OR `cat3` = $value[1]) AND `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1"));
	for ($i = 2; $i <= ceil ($count / $_CONFIG['list_arts']); ++$i) $links[] = '	<item type="web" url="' . rwl ('kategoria', "$value[1]-$value[0]-strana-$i") . '"></item>';
};



/*--- Sekcie ---*/
$query = mysql_query ("SELECT `seo`, `com`, `id` FROM `{$prefix}_sec` WHERE `id` != '{$_CONFIG['homepage']}'");
while ($value = mysql_fetch_row ($query)) {
	$links[] = '	<item type="web" url="' . rwl ('sekcia', "$value[2]-$value[0]") . '"></item>';
	if ($_CONFIG['global_comments'] == 1) {
		list ($count) = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM `{$prefix}_comments` WHERE `kde` = 'sec_$value[2]'"));
		for ($i = 2; $i <= ceil ($count / $_CONFIG['list_coms']); ++$i) $links[] = '	<item type="web" url="' . rwl ('sekcia', "$value[2]-$value[0]-strana-$i") . '"></item>';
	};
};



/*--- Články ---*/
$query = mysql_query ("SELECT `seo`, `id`, `coms` FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1");
while ($value = mysql_fetch_row ($query)) {
	$links[] = '	<item type="web" url="' . rwl ('clanok', "$value[1]-$value[0]") . '"></item>';
	if ($_CONFIG['global_comments'] == 1 and $value[2] == 1) {
		list ($count) = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM `{$prefix}_comments` WHERE `kde` = 'clanok_$value[1]'"));
		for ($i = 2; $i <= ceil ($count / $_CONFIG['list_coms']); ++$i) $links[] = '	<item type="web" url="' . rwl ('clanok', "$value[1]-$value[0]-komentare-$i") . '"></item>';
	};
};



/*--- Galérie ---*/
$query = mysql_query ("SELECT `skr`, `id` FROM `{$prefix}_gall` WHERE `showing` = 1");
while ($value = mysql_fetch_row ($query)) {
	$links[] = '	<item type="web" url="' . rwl ('galeria', "$value[1]-$value[0]") . '"></item>';
	list ($count) = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM `{$prefix}_img` WHERE `cat` = $value[1]"));
	for ($i = 2; $i <= ceil ($count / $_CONFIG['list_imgs']); ++$i) $links[] = '	<item type="web" url="' . rwl ('galeria', "$value[1]-$value[0]-strana-$i") . '"></item>';
	if ($_CONFIG['global_comments'] == 1) {
		list ($count) = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM `{$prefix}_comments` WHERE `kde` = 'cat_$value[1]'"));
		for ($i = 2; $i <= ceil ($count / $_CONFIG['list_coms']); ++$i) $links[] = '	<item type="web" url="' . rwl ('galeria', "$value[1]-$value[0]-komentare-$i") . '"></item>';
	};
};



/*--- Ostatné ---*/
list ($count) = mysql_query ("SELECT COUNT(*) FROM `{$prefix}_gall` WHERE `showing` = 1");
for ($i = 1; $i <= ceil ($count / $_CONFIG['list_cats']); ++$i) $links[] = '	<item type="web" url="' . rwl ('gallery', $i) . '"></item>';
$links[] = '	<item type="web" url="' . rwl ('stranka', 'news') . '"></item>';
list ($count) = mysql_query ("SELECT COUNT(*) FROM `{$prefix}_microblog`");
for ($i = 2; $i <= ceil ($count / $_CONFIG['list_microblog']); ++$i) $links[] = '	<item type="web" url="' . rwl ('stranka', "news-strana-$i") . '"></item>';

if ($_CONFIG['friends_mese'] == 1) {
	$sql = @mysql_query ("SELECT `a`.`id`, `a`.`type`, `a`.`nadpis`, `a`.`popis`, CONCAT_WS(' ', `a`.`nadpis`, `a`.`popis`, `b`.`nadpis`, `b`.`popis`) as `fulltext` FROM `{$prefix}_img` as `a`, `{$prefix}_gall` as `b` WHERE `a`.`cat` = `b`.`id`");
	while ($info = @mysql_fetch_assoc ($sql)) {
		if (empty ($info['fulltext'])) $info['fulltext'] = 'Obrázok ' . $info['id'];
		$links[] = '	<item type="' . $info['type'] . '" url="store/gallery/' . $info['id'] . '.' . $info['type'] . '" title="' . str_replace (array ('"', "'"), array ('&quot;', '&#39;'), strip_tags ($info['nadpis'])) . '" description="' . str_replace (array ('"', "'"), array ('&quot;', '&#39;'), strip_tags ($info['popis'])) . '">' . im_format ($info['fulltext']) . '</item>';
	};
	$sql = @mysql_query ("SELECT `imagelink`, `nadpis` FROM `{$prefix}_clanky` WHERE `imagelink` LIKE 'store/icons/%'");
	while ($info = @mysql_fetch_assoc ($sql)) {
		$type = substr ($info['imagelink'], 1 + strrpos ($info['imagelink'], '.'));
		$links[] = '	<item type="' . $type . '" url="' . $info['imagelink'] . '" title="' . str_replace (array ('"', "'"), array ('&quot;', '&#39;'), strip_tags ($info['nadpis'])) . '" description="' . str_replace (array ('"', "'"), array ('&quot;', '&#39;'), strip_tags ($info['nadpis'])) . '">' . im_format ($info['nadpis']) . '</item>';
	};
};
echo implode ("\n", $links) . "\n";
?>
</indexmap>