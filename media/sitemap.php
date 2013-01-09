<?php





/*--- ReWriteLink ---*/

function rwl ($index, $value) {
	global $_CONFIG;
	$out = ($_CONFIG['rewrite'] == 1) ? "$index-$value.html" : "?$index=$value";
	return $out;
};






/*--- Main ---*/

define ('in', true);
include ('get-config.php');
Header ('Content-type: text/xml');
$linker = str_replace ('media/sitemap.php', '', $_SERVER['PHP_SELF']);
$linker = 'http://' . $_SERVER['SERVER_NAME'] . $linker;
$query = @mysql_query ("SELECT * FROM {$prefix}_config");
while ($value = mysql_fetch_row ($query)) $_CONFIG[$value[0]] = $value[1];






/*--- MainOut ---*/
echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>' . $linker . '</loc>
		<lastmod>' . date ('Y-m-d') . '</lastmod>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>' . "\n";





/*--- Kategórie ---*/

$query = mysql_query ("SELECT skr, id FROM {$prefix}_cats WHERE showing = 1");
while ($value = mysql_fetch_row ($query)) {
	echo "\t<url>\n\t\t<loc>$linker" . rwl ('kategoria', $value[1].'-'.$value[0]) . "</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>0.9</priority>\n\t</url>\n";
	$kolko = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM {$prefix}_clanky WHERE (`cat` = $value[1] OR `cat2` = $value[1] OR `cat3` = $value[1]) AND showing = 1 AND `confirmed` = 1 AND `added` <= NOW()"));
	$limit = $_CONFIG['list_arts'];
	if ($kolko[0] > $limit) {
		for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) {if ($i != 1) echo "\t<url>\n\t\t<loc>$linker" . rwl ('kategoria', "$value[1]-$value[0]-strana-$i") . "</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";};
		if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) {if ($i != 1) echo "\t<url>\n\t\t<loc>$linker" . rwl ('kategoria', "$value[1]-$value[0]-strana-$i") . "</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";};
	};
};





/*--- Sekcie ---*/

$query = mysql_query ("SELECT seo, com, id FROM {$prefix}_sec");
while ($value = mysql_fetch_row ($query)) {
	echo "\t<url>\n\t\t<loc>$linker" . rwl ('sekcia', $value[2].'-'.$value[0]) . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.7</priority>\n\t</url>\n";
	$kolko = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM {$prefix}_comments WHERE kde = 'sec_$value[2]'"));
	$limit = $_CONFIG['list_coms'];
	if ($kolko[0] > $limit and $value[1] == 1) {
		for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) {if ($i != 1) echo "\t<url>\n\t\t<loc>$linker" . rwl ('sekcia', "$value[2]-$value[0]-strana-$i") . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";};
		if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) {if ($i != 1) echo "\t<url>\n\t\t<loc>$linker" . rwl ('sekcia', "$value[2]-$value[0]-strana-$i") . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";};
	};
};





/*--- Články ---*/

$query = mysql_query ("SELECT seo, DATE_FORMAT(added, '%Y-%m-%d'), id, coms FROM {$prefix}_clanky WHERE showing = 1 AND added <= NOW() AND `confirmed` = 1");
while ($value = mysql_fetch_row ($query)) {
	$lastcom = mysql_fetch_row (mysql_query ("SELECT DATE_FORMAT(added, '%Y-%m-%d') FROM {$prefix}_comments WHERE kde = 'clanok_$value[2]' ORDER BY id DESC LIMIT 1"));
	if ($lastcom[0] !== false and str_replace ('-', '', $lastcom[0]) > str_replace ('-', '', $value[1])) $value[1] = $lastcom[0];
	echo "\t<url>\n\t\t<loc>$linker" . rwl ('clanok', $value[2].'-'.$value[0]) . "</loc>\n\t\t<lastmod>$value[1]</lastmod>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";
	$kolko = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM {$prefix}_comments WHERE kde = 'clanok_$value[2]'"));
	$limit = $_CONFIG['list_coms'];
	if ($kolko[0] > $limit and $value[3] == 1) {
		for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) {if ($i != 1) echo "\t<url>\n\t\t<loc>$linker" . rwl ('clanok', "$value[2]-$value[0]-komentare-$i") . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.5</priority>\n\t</url>\n";};
		if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) {
			if ($i != 1) echo "\t<url>\n\t\t<loc>$linker" . rwl ('clanok', "$value[2]-$value[0]-komentare-$i") . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.5</priority>\n\t</url>\n";
		};
	};

};





/*--- Galérie ---*/

$query = mysql_query ("SELECT skr, id FROM {$prefix}_gall WHERE showing = 1");
while ($value = mysql_fetch_row ($query)) {
$lastimg = mysql_fetch_row (mysql_query ("SELECT DATE_FORMAT(added, '%Y-%m-%d') FROM {$prefix}_img WHERE cat = '$value[1]' ORDER BY id DESC LIMIT 1"));
$lastcom = mysql_fetch_row (mysql_query ("SELECT DATE_FORMAT(added, '%Y-%m-%d') FROM {$prefix}_comments WHERE kde = 'cat_$value[1]' ORDER BY id DESC LIMIT 1"));
if ($lastimg[0] === false) $date = date ("Y-m-d"); else $date = $lastimg[0];
if ($lastcom[0] !== false and str_replace ('-', '', $lastcom[0]) > str_replace ('-', '', $date)) $date = $lastcom[0];
echo "\t<url>\n\t\t<loc>$linker" . rwl ('galeria', $value[1].'-'.$value[0]) . "</loc>\n\t\t<lastmod>$date</lastmod>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";};





/*--- Galéria ---*/

$kolko = mysql_query ("SELECT COUNT(*) FROM {$prefix}_gall WHERE showing = 1");
$limit = $_CONFIG['list_cats'];
if ($kolko[0] > $limit) {
	for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) echo "\t<url>\n\t\t<loc>$linker" . rwl ('gallery', $i) . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
	if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) echo "\t<url>\n\t\t<loc>$linker" . rwl ('gallery', $i) . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";
} else echo "\t<url>\n\t\t<loc>$linker" . rwl ('gallery', 1) . "</loc>\n\t\t<changefreq>weekly</changefreq>\n\t\t<priority>0.8</priority>\n\t</url>\n";;

echo "\t<url>\n\t\t<loc>$linker" . rwl ('stranka', 'news') . "</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>0.9</priority>\n\t</url>\n";
echo "\t<url>\n\t\t<loc>$linker" . rwl ('stranka', 'rss-feed') . "</loc>\n\t\t<changefreq>monthly</changefreq>\n\t\t<priority>0.7</priority>\n\t</url>\n";
echo "\t<url>\n\t\t<loc>$linker" . rwl ('stranka', 'sitemap') . "</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>0.6</priority>\n\t</url>\n";



/*--- END ---*/
echo '</urlset>';
?>
