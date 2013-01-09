<?php


/*--- Základná funkcionalita ---*/

include_once ('codes/texyla/texyla.php');
function rwl ($index, $value) {
	global $_CONFIG;
	$out = ($_CONFIG['rewrite'] == 1) ? "$index-$value.html" : "?$index=$value";
	return $out;};
function GetTrueRssType ($type) {
	switch ($type) {
		case 'komentare': return 'komentare'; break;
		case 'news':
		case 'microblog': return 'news'; break;
		default: return 'clanky'; break;};};
function GetNameOfRssType ($type) {
	global $_CONFIG;
	switch ($type) {
		case 'komentare': return $translate['comments']; break;
		case 'news': return $_CONFIG['microblog_head']; break;
		default: return $translate['articles']; break;};};
function ItemOutFormat ($text, $comments = false) {
	$text = texyla ($text, 'admin');
	$array[] = array ("\n", "\r", "\t", '  ');
	$array[] = array (' ', ' ', '', ' ');
	$text = str_replace ($array[0], $array[1], $text);
	if ($comments === true) {
		while (false !== ($tag[0] = strpos ($text, '[reply:')) and false !== ($tag[1] = strpos ($text, ']', $tag[0]))) {
			foreach ($tag as $index => $value) $tag[$index] = '' . $value;
			$string = substr ($text, $tag[0], $tag[1]  - $tag[0] + 1);
			$text = str_replace ($string, '', $text);
			unset ($tag);
		};
	};
	if (substr ($text, 0, 1) == ' ') $text = substr ($text, 1);
	if (substr ($text, -1, 1) == ' ') $text = substr ($text, 0, -1);
	$text = strip_tags ($text);
	return $text;
};





/*--- Main ---*/
include ('media/get-config.php');
if (file_exists ("languages/{$_CONFIG['language']}.php")) {
	include ("languages/{$_CONFIG['language']}.php");
} else exit (chyba ('nie je možné načítať preklad systému!'));
Header ('content-type: text/xml; charset=UTF-8');
if (isset ($_GET['w']) and $_GET['w'] != '') $_GET['what'] = $_GET['w'];
if (isset ($_GET['what']) and $_GET['what'] != '') $type = GetTrueRssType ($_GET['what']); else $type = 'clanky';
$linker = str_replace ('rss.php', '', $_SERVER['PHP_SELF']);
$linker = 'http://' . $_SERVER["SERVER_NAME"] . $linker;
if (false === @mysql_fetch_row (@mysql_query ("SELECT id FROM {$prefix}_iplog WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND what = 'rss' LIMIT 1")))
@mysql_query ("INSERT INTO {$prefix}_iplog VALUES (0, '{$_SERVER['REMOTE_ADDR']}', 'rss', '');");






/*--- MainOut ---*/

echo '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<generator>Opiner CMS</generator>' . "\n";
if (file_exists ('media/favicon.png'))
echo "		<image>
			<link>$linker</link>
			<title><![CDATA[{$_CONFIG['title']}]]></title>
			<url>{$linker}media/favicon.png</url>
		</image>\n";
echo "		<title><![CDATA[{$_CONFIG['title']}]]></title>
		<link>$linker</link>
		<description><![CDATA[{$_CONFIG['desc']}]]></description>
		<language>sk</language>
		<pubDate>" . date ("D, d M Y H:i:s") . " +0100</pubDate>
		<lastBuildDate>" . date ("D, d M Y H:i:s") . " +0100</lastBuildDate>
		<webMaster><![CDATA[{$_CONFIG['author']}]]></webMaster>\n";





/*--- QUERY TAKEN ---*/

switch ($type) {





	/*--- Komentáre ---*/
	case 'komentare':
	if ($_CONFIG['global_comments'] == 1) {
		$sql = mysql_query ("SELECT id, kde, text, UNIX_TIMESTAMP(`added`), autor FROM {$prefix}_comments WHERE kde != 'fasttext' ORDER BY id DESC LIMIT {$_CONFIG['list_rss']}");
		while ($tab = @mysql_fetch_row($sql)) {
			if (substr ($tab[1], 0, 6) == 'clanok') {
				$info = mysql_fetch_row (mysql_query ("SELECT seo, id FROM {$prefix}_clanky WHERE id = '" . substr ($tab[1], 7) . "'"));
				$link = $linker . rwl ('clanok', $info[1].'-'.$info[0]) . '#comment-' . $tab[0];
			} else if (substr ($tab[1], 0, 3) == 'cat') {
				$info = mysql_fetch_row (mysql_query ("SELECT skr,id FROM {$prefix}_gall WHERE id = '" . substr ($tab[1], 4) . "'"));
				$link = $linker . rwl ('galeria', $info[1].'-'.$info[0]) . '#comment-' . $tab[0];
			} else if (substr ($tab[1], 0, 3) == 'sec') {
				$info = mysql_fetch_row (mysql_query ("SELECT seo,id FROM {$prefix}_sec WHERE id = '" . substr ($tab[1], 4) . "'"));
				$link = $linker . rwl ('sekcia', $info[1].'-'.$info[0]) . '#comment-' . $tab[0];
			} else if (substr ($tab[1], 0, 3) == 'img') {
				$link = $linker . 'pviewer.php?img=' . substr ($tab[1], 4) . '#comment-' . $tab[0];
			} else if ($tab[1] == 'kniha') {
				$link = $linker . rwl ('kniha', 1) . '#comment-' . $tab[0];
			} else $link = $linker;
			$tab[2] = ItemOutFormat ($tab[2], true);
			if ($tab[4] == 'admin') $tab[4] = $_CONFIG['author'];
			echo "		<item>
			<title><![CDATA[" . strip_tags ($tab[4]) . "]]></title>
			<author><![CDATA[" . strip_tags ($tab[4]) . "]]></author>
			<description><![CDATA[" . strip_tags ($tab[2]) . "]]></description>
			<pubDate>" . date ('r', $tab[3]) . "</pubDate>
			<link><![CDATA[$link]]></link>
		</item>\n";
		};
	};
	break;





	/*--- MikroBlog ---*/
	case 'news':
	$sql = mysql_query ("SELECT text, DATE_FORMAT(added, '%a, %d %b %Y %H:%i:%s +0100') FROM {$prefix}_microblog ORDER BY id DESC LIMIT {$_CONFIG['list_rss']}");
	while ($tab = @mysql_fetch_row($sql)) {
		$tab[0] = ItemOutFormat ($tab[0]);
		echo "		<item>
			<title><![CDATA[{$_CONFIG['microblog_head']}]]></title>
			<link><![CDATA[$linker" . rwl ('stranka', 'news') . "]]></link>
			<description><![CDATA[$tab[0]]]></description>
			<pubDate>$tab[1]</pubDate>
			<author><![CDATA[{$_CONFIG['author']}]]></author>
		</item>\n";
	};
	break;





	/*--- Články ---*/
	default:
	$sql = mysql_query ("SELECT `id`, `nadpis`, `seo`, CONCAT(`perex`, '\n\n', `text`) as `clanok`, UNIX_TIMESTAMP(`added`) as `time`, `autor` FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1 ORDER BY `added` DESC LIMIT {$_CONFIG['list_rss']}");
	while ($tab = @mysql_fetch_assoc ($sql)) {
		if ($_CONFIG['wysiwyg'] == 'texyla') $tab['clanok'] = texyla ($tab['clanok'], 'admin');
		$tab['autor'] = ($info = @mysql_fetch_row (@mysql_query ("SELECT `name` FROM `{$prefix}_moderatos` WHERE `id` = " . $tab['autor']))) ? $info[0] : $_CONFIG['author'];
		echo "		<item>
			<title><![CDATA[" . $tab['nadpis'] . "]]></title>
			<author><![CDATA[" . $tab['autor'] . "]]></author>
			<description><![CDATA[" . $tab['clanok'] . "]]></description>
			<link><![CDATA[$linker" . rwl ('clanok', $tab['id'] . '-' . $tab['seo']) . "]]></link>
			<pubDate>" . date ('r', $tab['time']) . "</pubDate>
		</item>\n";
	};
	break;

};




/*--- END ---*/
echo"	</channel>
</rss>";
?>
