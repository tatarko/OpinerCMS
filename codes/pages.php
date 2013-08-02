<?php

/*--- CORE ---*/
if (!defined ('in')) exit ();
if (isset ($_REQUEST['page'])) $_REQUEST['stranka'] = $_REQUEST['page'];
if (false !== ($strrpos = strrpos ($_REQUEST['stranka'], '-strana-'))) {
	$pag = substr ($_REQUEST['stranka'], $strrpos + 8);
	$_REQUEST['stranka'] = substr ($_REQUEST['stranka'], 0, $strrpos);
} else $pag = false;
switch ($_REQUEST['stranka']) {










/*----- MICROBLOG -----*/





case 'microblog':
case 'news':
$_META['keywords'] = $_CONFIG['microblog_head'] . ', ' . $translate['microblog.keys'];
$_META['description'] = $_CONFIG['microblog_head'] . ', ' . $translate['microblog.desc'];
if ($_CONFIG['twittername'] != '' and (!isset ($cache['twittercheck']) or $cache['twittercheck'] < time()-$_CONFIG['twittertime']*60)
and $xml = @simplexml_load_file ('http://twitter.com/statuses/user_timeline.xml?screen_name=' . $_CONFIG['twittername'] . '&count=5') and isset ($xml->status)) {
	foreach ($xml -> status as $status) {
		$text = adjust (preg_replace (array ("/@([A-Za-z0-9_]*)/i", "/#([A-Za-z0-9_]*)/i"), array ('"@$1":http://twitter.com/$1', '"#$1":http://twitter.com/#search?q=%23$1'), $status -> text) . " (*\"Twitter\":http://twitter.com/*)");
		$datetime = date ('Y-m-d H:i:s', strtotime ($status -> created_at));
		if (!@mysql_fetch_row (mysql_query ("SELECT `id` FROM `{$prefix}_microblog` WHERE `text` = '" . $text . "' LIMIT 1")))
		@mysql_query ("INSERT INTO `{$prefix}_microblog` VALUES (0, 0, '" . $datetime . "', '" . $text . "');");
	};
	$cache['twittercheck'] = time();
};
$title .= $sep . $_CONFIG['microblog_head'];
$out = '<h1 align="center">' . $_CONFIG['microblog_head'] . '</h1>
<style type="text/css">
.div1, .div2 {border:1px solid ' . $template->config['theme-colors']['ap3'] . ';margin:0 0 15px;padding:8px 10px;-moz-border-radius:5px;-webkit-border-radius:5px;}
.div1 p, .div2 p {margin:0;padding:0;font-size:10px;}
.div1 .added, .div2 .added {margin-bottom:5px;font-size:11px;font-weight:bold;}
.div1 {background-color:' . $template->config['theme-colors']['ap1'] . ';}
.div2 {background-color:' . $template->config['theme-colors']['ap2'] . ';}
.linker {font-weight:bold;text-decoration:none;}
</style>' . n;
$out .= OpinerAutoLoader::texyla ($_CONFIG['microblog'], 'forum');
$kolko = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM {$prefix}_microblog"));
if ($pag === false)
$pag = ceil ($kolko[0] / $_CONFIG['list_microblog']);
else $title .= $sep . $pag . '. strana';
if (($pag-1) * $_CONFIG['list_microblog'] > $kolko[0]) $pag = ceil ($kolko[0] / $_CONFIG['list_microblog']);
if ($pag == 0) $pag = 1;

if ($kolko[0] > $_CONFIG['list_microblog']) {
	unset ($array);
	for ($i = 1, $all = ceil ($kolko[0]/$_CONFIG['list_microblog']); $i <= $all; ++$i) {
		if ($i <= 5 or ($i >= ($pag - 2) and $i <= ($pag + 2)) or $i >= ($all - 4))
		$array[$i] = '<a href="' . rwl ('stranka', 'news-strana-' . $i) . '" class="linker">'.$i.'</a>';
		else if ($ind = $i - 1 and isset ($array[$ind]) and $array[$ind] != '...')
		$array[$i] = '...';
	};
	$array[$pag] = str_replace (">$pag<", ">[$pag]<", $array[$pag]);
	$p = '<p align="center">' . implode (n, $array) . '</p>'.n;
} else $p = '';

$db_select = $_CONFIG['list_microblog'] * ($pag - 1);
$sql = @mysql_query ("SELECT id, DATE_FORMAT(added, '%d.%m.%Y, %H:%i'), text, autor FROM {$prefix}_microblog ORDER BY added ASC LIMIT $db_select, {$_CONFIG['list_microblog']}");
if (@mysql_num_rows ($sql) > 0) {
	$IID = 0;
	while ($tab = @mysql_fetch_row ($sql)) {
		$out .= '<div class="div' . ((++$IID % 2 == 0) ? 1 : 2) . '">' . n;
		$out .= '<p class="added">' . $tab[1] . ' (' . GetAuthorName ($tab[3]) . ')';
		if (ONLINE and (ADMIN or $tab[3] == USER)) {
			$out .= ' <a href="admin.php?what=microblog&mod=edit&id=' . $tab[0] . '">' . $translate['edit'] . '</a>,';
			$out .= ' <a href="admin.php?what=microblog&mod=delete&id=' . $tab[0] . '">' . $translate['drop'] . '</a>';
		};
		$out .= '</p>';
		$out .= OpinerAutoLoader::texyla ($tab[2], 'admin');
		$out .= '</div>' . n;
	};
} else $out .= '<p>' . $translate['nocontent'] . '</p>' . n;
$out .= $p;
if (ONLINE) {
	$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
	$out .= '<h2>' . $translate['add'] . '</h2>
<form action="admin.php?what=microblog&mod=add" method="post">
	<textarea id="blogpost" name="blogpost" rows="3" style="width:100%;"></textarea>
	<script type="text/javascript">
		opt = Texyla.configurator.forum ("blogpost");
		opt.editorWidth = 450;
		opt.submitButton = true;
		new Texyla (opt);
	</script>
</form>'.n;};
break;









/*----- VOTE -----*/





case 'vote':
	if (isset ($_POST['id'], $_POST['vote']) and false !== ($info1 = mysql_fetch_row (mysql_query ("SELECT id, votes, locked FROM {$prefix}_polls WHERE id = '{$_POST['id']}' LIMIT 1")))
	and $info1[2] == 0 and false === mysql_fetch_row (mysql_query ("SELECT id FROM {$prefix}_iplog WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND what = 'poll-{$_POST['id']}'"))) {
		$votes = explode ('#', $info1[1]);
		$votes[$_POST['vote']] = $votes[$_POST['vote']] + 1;
		$votes = implode ('#', $votes);
		mysql_query ("UPDATE {$prefix}_polls SET votes = '$votes' WHERE id = '$info1[0]' LIMIT 1");
		mysql_query ("INSERT INTO {$prefix}_iplog VALUES (0, '{$_SERVER['REMOTE_ADDR']}', 'poll-$info1[0]', '{$_POST['vote']}');");
	};
	Header ('Location: '.$_SERVER['HTTP_REFERER']);
break;









/*----- COMMENT-ADD -----*/





case 'comment-add':
$out = '<h1 align="center">' . $translate['comments.add'] . '</h1>'.n;
$title .= $sep . $translate['comments.add'];
if (ONLINE) {
	unset ($_SESSION['as']);
	if ($info = @mysql_fetch_row (@mysql_query ("SELECT `nick` FROM `{$prefix}_moderators` WHERE `id` = ".USER)))
	@mysql_query ("INSERT INTO {$prefix}_comments VALUES (0,'".$_POST["id"]."','{$info[0]}',NOW(),'{$_POST["txt"]}','{$_SERVER["REMOTE_ADDR"]}','','')");
	else @mysql_query ("INSERT INTO {$prefix}_comments VALUES (0,'".$_POST["id"]."','admin',NOW(),'{$_POST["txt"]}','{$_SERVER["REMOTE_ADDR"]}','','')");
	$IID = mysql_insert_id ();
	Header ('Location: ' . $_POST['ref'] . '#comment-' . $IID);
};
if (isset ($_POST['meno'], $_POST['txt'], $_POST['mail'], $_POST['web'], $_POST['id'], $_POST['ref'])
and !empty ($_POST['meno']) and !empty ($_POST['txt']) and !empty ($_POST['id']) and !empty ($_POST['ref'])) {
	if (array_search ($_POST['meno'], array ('admin', $_CONFIG['author'], $_CONFIG['loginnick'])) === false
	and false === @mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_moderators` WHERE `nick` = '" . adjust ($_POST['meno']) . "' OR `name` = '" . adjust ($_POST['meno']) . "' LIMIT 1"))) {
		if ($_CONFIG['antispam'] == 0 or (isset ($_POST['as']) and $_SESSION['as'] == sha1 ($_POST['as']))) {
			unset ($_SESSION['as']);
			@mysql_query ("INSERT INTO `{$prefix}_comments` VALUES (0, '" . adjust ($_POST['id']) . "', '" . adjust ($_POST['meno']) . "', NOW(), '" . adjust ($_POST['txt']) . "', '" . adjust ($_SERVER['REMOTE_ADDR']) . "', '" . adjust ($_POST['mail']) . "', '" . adjust ($_POST['web']) . "');");
			SetCookie ('comments-default-values', implode ('~$~', array ($_POST['meno'], $_POST['mail'], $_POST['web'])), time () + (3600 * 24 * 31));
			$IID = mysql_insert_id ();
			Header ('Location: ' . $_POST['ref'] . '#comment-' . $IID);
		} else $out .= '<p>' . $translate['er10'];
	} else $out .= '<p>' . $translate['er11'];
} else $out .= '<p>' . $translate['ee12'];
$tohead[] = '<meta http-equiv="Refresh" name="Expires" content="2, URL=' . $_POST['ref'] . '">';
$out .= '<br />' . $translate['er13'] . '</p>';
break;










/*----- COMMENT-DELETE -----*/





case 'comment-delete':
if (ONLINE) {
$out = '<h1 align="center">' . $translate['comments.manage'] . '</h1>'.n;
$title .= $sep . $translate['comments.manage'];



/*--- POST ---*/
if (isset ($_POST['ok'])) {
	if ($_POST['ok'] == $translate['yes']) {
		@mysql_query ("DELETE FROM {$prefix}_comments WHERE id = {$_POST['id']} LIMIT 1");
		Header ('Location: ' . $_POST['ref'] . '#comments');
	};
	Header ('Location: ' . $_POST['ref'] . '#comments');
} else {



/*--- FORM ---*/
$out .= '<form action="./" method="post">
<input type="hidden" name="page" value="comment-delete" />
<input type="hidden" name="ref" value="' . $_SERVER['HTTP_REFERER'] . '" />
<input type="hidden" name="id" value="' . $_GET['id'] . '" />
<p>' . $translate['sureact'] . '</p>
<input type="submit" name="ok" value="' . $translate['yes'] . '" />
<input type="submit" name="ok" value="' . $translate['no'] . '" />
</form>';};
} else Header ('Location: ' . $_POST['ref'] . '#comments');
break;










/*----- COMMENT-EDIT -----*/





case 'comment-edit':
if (ONLINE) {
$out = '<h1 align="center">' . $translate['comments.manage'] . '</h1>'.n;
$title .= $sep . $translate['comments.manage'];



/*--- POST ---*/
if (!empty ($_POST)) {
	@mysql_query ("UPDATE {$prefix}_comments SET text='{$_POST['text']}', autor = '{$_POST['autor']}' WHERE id = {$_POST['id']} LIMIT 1");
	Header ('Location: ' . $_POST['ref'] . '#comments');
};



/*--- FORM ---*/
if ($info = @mysql_fetch_row (@mysql_query ("SELECT text, autor FROM {$prefix}_comments WHERE id = {$_GET['id']} LIMIT 1"))) {
$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
$out .= '<form action="./" method="post">
<input type="hidden" name="page" value="comment-edit" />
<input type="hidden" name="ref" value="' . $_SERVER['HTTP_REFERER'] . '" />
<input type="hidden" name="id" value="' . $_GET['id'] . '" />
<input type="text" name="autor" value="' . $info[1] . '" />
<textarea id="text" name="text" rows="3" cols="50">' . $info[0] . '</textarea>
<script type="text/javascript">
	options = Texyla.configurator.forum ("text");
	options.editorWidth = 400;
	new Texyla (options);
</script>
</form>';
} else Header ('Location: ' . $_POST['ref'] . '#comments');
} else Header ('Location: ' . $_POST['ref'] . '#comments');
break;










/*----- RSS-FEED -----*/





case 'rss-feed':
$_META['keywords'] = $translate['rss.keys'];
$_META['description'] = $translate['rss.keys'];
$title .= $sep . $translate['links.rss'];
$where = str_replace ('index.php', '', $_SERVER['PHP_SELF']);
$where = 'http://' . $_SERVER['SERVER_NAME'] . $where;
$out = '<h1 align="center">' . $translate['links.rss'] . '</h1>
<p>' . $translate['rss.note'] . '</p>
<ul>
	<li>' . $translate['articles'] . ': <a href="' . $where . 'rss.php">' . $where . 'rss.php</a></li>
	<li>'.$_CONFIG['microblog_head'].': <a href="' . $where . 'rss.php?what=news">' . $where . 'rss.php?what=news</a></li>
	<li>' . $translate['comments'] . ': <a href="' . $where . 'rss.php?what=komentare">' . $where . 'rss.php?what=komentare</a></li>
</ul>';
break;










/*----- Hlasovanie za články -----*/





case 'article-votings':
if (!isset ($_POST['ArtID']) or $_POST['ArtID'] == '') Header ('Location: ./');
$kde = 'hodnotenie_art_' . $_POST['ArtID'];
$IP = $_SERVER['REMOTE_ADDR'];
@mysql_query ("INSERT INTO {$prefix}_iplog VALUES (0, '$IP', '$kde', '{$_POST['PerHight']}')");
Header ('Location: ' . $_SERVER['HTTP_REFERER']);
break;











/*----- SEARCH -----*/





case 'search':
$_META['keywords'] = $translate['search.keys'];
$_META['description'] = $translate['search.desc'];
$template -> index = false;
$out = '<h1 align="center">' . $translate['tsearch'];
if (isset ($_REQUEST['tag']) and !empty ($_REQUEST['tag'])) {
 $title .= $sep . $translate['tsearch'] . ' "' . $_REQUEST['tag'] . '"';
 $out .= $sep . $translate['arts.tags'] . '</h1>
 <form action="./" method="post">
  <input type="hidden" name="page" value="search" />
  <input type="text" name="tag" value="'.setQ ($_REQUEST['tag']).'" style="width:70%;float:left;" />
  <input type="submit" value="' . $translate['ssearch'] . '" style="width:25%;float:right;" />
 </form>
 <br />' . n;
 $sql = @mysql_query ("SELECT `id`, `nadpis`, `seo`, `perex` FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1 AND `tags` LIKE '%" . adjust ($_REQUEST['tag']) . "%' ORDER BY `added` DESC");
 while ($tab = mysql_fetch_assoc ($sql)) {
  $link = _SiteLink . rwl ('clanok', $tab['id'] . '-' . $tab['seo']);
  $text = strip_tags (OpinerAutoLoader::texyla (HcmParser ($tab['perex']), 'admin'));
  #$text = preg_replace ("#[\s]+#", " ", $text);
  $text = strlen ($text) > 256 ? substr ($text, 0, strpos ($text, ' ', 256)) . '...' : $text;
  $out .= '  <h3><a href="' . $link . '">' . strip_tags ($tab['nadpis']) . '</a></h2>
  <p>' . $text . '<br /><a href="' . $link . '">' . $link . '</a></p>'.n;
 }; 
} else {
$_REQUEST['search'] = isset ($_REQUEST['search']) ? str_replace (array ("\\'", '\\"', '\\\\'), array ("'", '"', '\\'), $_REQUEST['search']) : '';
$title .= $sep . $translate['tsearch'] . ' "' . $_REQUEST['search'] . '"';
$title .= (isset ($_REQUEST['pag']) and $_REQUEST['pag'] != 1) ? $sep . langrep ('page', $_REQUEST['pag']) : '';
$pag = (isset ($_REQUEST['pag'])) ? $_REQUEST['pag'] : 1;
$out .= '</h1>
<form action="./" method="post">
<input type="hidden" name="page" value="search" />
<input type="text" name="search" value="'.setQ($_REQUEST['search']).'" style="width:70%;float:left;" />
<input type="submit" value="' . $translate['ssearch'] . '" style="width:25%;float:right;" />
</form>' . n;
$limit = 25;




/*--- Creating Qeury ---*/

if (strlen ($_REQUEST['search']) >= 3) {
	$limit2 = ($pag-1)*$limit;
	$where = adjust ($_REQUEST['search']);
	list ($kolko) = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1 AND MATCH(`nadpis`, `perex`, `text`) AGAINST('" . $where . "' IN BOOLEAN MODE)"));
	@mysql_query ("INSERT INTO `{$prefix}_iplog` VALUES (0, '" . adjust ($_SERVER['REMOTE_ADDR']) . "', 'search', '" . $where . "');");
	$querymain = "SELECT `seo`, `nadpis`, `perex`, `id` FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1 AND MATCH(`nadpis`, `perex`, `text`) AGAINST('" . $where . "' IN BOOLEAN MODE) ORDER BY MATCH(`nadpis`, `perex`, `text`) AGAINST('" . $where . "' IN BOOLEAN MODE) DESC LIMIT $limit2, $limit";
	$sqlmain = @mysql_query ($querymain);
	
	
	
	/*--- Stránkovanie ---*/
	
	if ($kolko > $limit) {
		$form = '<form action="./" method="post">
		<input type="hidden" name="page" value="search" />
		<input type="hidden" name="search" value="'.setQ($_REQUEST['search']).'" />' . n;
		for ($i = 1; $kolko - ($limit * $i) > 0; ++$i) @$table .= '<td><input type="submit" name="pag" value="' . $i . '" /></td>' . n;
		if ($kolko - ($limit * ($i - 1)) + 1 > 0 and $i != 1) @$table .= '<td><input type="submit" name="pag" value="' . $i . '" /></td>' . n;
	} else $table = '';
	
	
	
	/*--- Lišta ---*/
	
	$first = $limit2 + 1;
	$of = ($first > $limit) ? ($first + 9) : $limit;
	if ($of == $limit) $of = ($kolko < $limit) ? $kolko : $limit;
	if ($of > $kolko) $of = $kolko;
	if (isset ($form)) $out .= $form;
	if ($kolko == 0) $first = 0;
	$out .= '<table align="right" style="float:right;"><tr><td>' . langrep ('dsearch', $first, $of, $kolko) . '</td>' . n . $table . '</tr></table>'.n;
	if (isset ($form)) $out .= '</form>'.n;
	$out .= '<div class="cleanL">&nbsp;</div><br />' . n;
	
	
	
	/*--- Prechod databázou ---*/
	
	if (@mysql_num_rows ($sqlmain) != 0) {
		while ($tab = @mysql_fetch_row ($sqlmain)) {
			$text = strip_tags (texyla (HcmParser ($tab[2]), 'admin'));
			$text = strlen ($text) > 256 ? substr ($text, 0, strpos ($text, ' ', 256)) . '...' : $text;
			$out .= '<h2>' . $tab[1] . '</h2>
<p>' . $text . '<br /><a href="' . rwl ('clanok', $tab[3] . '-' . $tab[0]) . '">' . _SiteLink . rwl ('clanok', $tab[3] . '-' . $tab[0]) . "</a></p>\n";
		};
	} else $out .= "<p>{$translate['er14']}</p>\n";
} else $out .= "<p>{$translate['er15']}</p>\n";
};
break;










/*----- SITEMAP -----*/





case 'sitemap':
$_META['keywords'] = $translate['sitemap.keys'];
$_META['description'] = $translate['sitemap.desc'];
$title .= $sep . $translate['links.sitemap'];
$out = '<h1 align="center">' . $translate['links.sitemap'] . '</h1>'.n;

$out .= "<h2>{$translate['sections']}</h2>\n<ul>\n";
$sql = @mysql_query("SELECT nadpis, id, seo FROM {$prefix}_sec ORDER BY nadpis ASC");
while ($tab = mysql_fetch_row($sql))
$out .= "\t<li><a href=\"" . rwl ('sekcia', $tab[1].'-'.$tab[2]) . "\">$tab[0]</a></li>\n";
$out .= "</ul>\n";

$out .= "<h2>{$translate['categories']}</h2>\n<ul>\n";
$sql = @mysql_query("SELECT nadpis, skr, id FROM {$prefix}_cats WHERE showing = 1 ORDER BY nadpis ASC");
while ($tab = mysql_fetch_row($sql))
$out .= "\t<li><a href=\"" . rwl ('kategoria', $tab[2].'-'.$tab[1]) . "\">$tab[0]</a></li>\n";
$out .= "</ul>\n";

$out .= "<h2>{$translate['articles']}</h2>\n<ul>\n";
$sql = @mysql_query("SELECT nadpis,id,seo FROM {$prefix}_clanky WHERE showing = 1 AND added <= NOW() AND `confirmed` = 1 ORDER BY nadpis ASC");
while ($tab = mysql_fetch_row($sql))
$out .= "\t<li><a href=\"" . rwl ('clanok', $tab[1].'-'.$tab[2]) . "\">$tab[0]</a></li>\n";
$out .= "</ul>\n";

$out .= "<h2>{$translate['albums']}</h2>\n<ul>\n";
$sql = @mysql_query("SELECT nadpis,skr,id FROM {$prefix}_gall WHERE showing = 1 ORDER BY nadpis ASC");
while ($tab = mysql_fetch_row($sql))
$out .= "\t<li><a href=\"" . rwl ('galeria', $tab[2].'-'.$tab[1]) . "\">$tab[0]</a></li>\n";
$out .= "</ul>\n";
break;











/*----- EMAIL -----*/





case 'email':
	if (isset ($_POST ['who'], $_POST ['sender'], $_POST ['subject'], $_POST ['text']))
	mail ($_POST['who'], $_POST['subject'], $_POST['text'], implode ("\r\n", array (
		'MIME-Version: 1.0',
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . $_POST['sender'],
		'Reply-To: ' . $_POST['sender'],
		'Return-Path: ' . $_POST['sender'],
		'X-Mailer: Opiner CMS'
	)));
	Header ('Location: ./');
break;











/*----- ARCHÍV ČLÁNKOV -----*/





case 'archiv':
$_META['keywords'] = $translate['arch.keys'];
$_META['description'] = $translate['arch.desc'];
$title .= $sep . $translate['links.archive'];
$out = '<h1 align="center">' . $translate['links.archive'] . '</h1>
<p>';
$sql = @mysql_query ("SELECT DISTINCT DATE_FORMAT(`added`, '%Y%m') FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1 ORDER BY `added` DESC");
if (@mysql_num_rows ($sql) == 0) {
	$out .= $translate['nocontent'] . '</p>';
	break;
};
$array = array ('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
while ($result = @mysql_fetch_row ($sql)) {
	$all[$result[0]] = $result[0];
	$years[] = substr ($result[0], 0, 4);
};
foreach (array_unique ($years) as $year) {
	$out .= "<strong>$year:</strong>";
	foreach ($array as $month) {
		if (isset ($all[$year.$month]))
		$out .= ' &nbsp; <a href="' . rwl ('stranka', 'archiv', true) .	'filter=' . $year . $month . '">' . $translate['ms' . $month] . '</a>';
		else $out .= " &nbsp; <em>{$translate["ms$month"]}</em>";
	};
	$out .= '<br />'.n;
};
$out .= '<a href="' . rwl ('stranka', 'archiv') . '">' . $translate['arts.showall'] . '</a></p>'.n;
$limit = 25;
$where = isset ($_REQUEST['filter']) ? " AND DATE_FORMAT(`added`, '%Y%m') = '" . adjust ($_REQUEST['filter'], true) . "'" : '';
list ($count) = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1$where"));
$pages = ceil ($count / $limit);
$apage = (isset ($_REQUEST['apage']) and $pag = adjust ($_REQUEST['apage'], true) and !empty ($pag) and ($pag - 1) * $limit <= $count) ? $pag : 1;
$sql = @mysql_query ("SELECT `id`, `nadpis`, `seo`, DATE_FORMAT(`added`, '%d.%m.%Y %H:%i') as `df` FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1$where ORDER BY `added` DESC LIMIT " . (($apage - 1) * $limit) . ", $limit");
if (@mysql_num_rows ($sql) == 0) {
	$out .= '<p>' . $translate['nocontent'] . '</p>';
	break;
};
$out .= '<ul>'.n;
while ($result = @mysql_fetch_assoc ($sql))
$out .= '	<li><a href="' . rwl ('clanok', $result['id'] . '-' . $result['seo']) . '">' . $result['nadpis'] . '</a> <em>(' . $result['df'] . ')</em></li>'.n;
$out .= '</ul>'.n;
if ($pages > 1) {
	$link = isset ($_REQUEST['filter']) ? rwl ('stranka', 'archiv', true) . 'filter=' . adjust ($_REQUEST['filter'], true) . '&amp;' : rwl ('stranka', 'archiv', true);
	$out .= '<p>'.n;
	for ($i = 1; $i <= $pages; ++$i)
	$out .= '<a href="' . $link . 'apage=' . $i . '">' . (($i == $apage) ? "<strong>$i</strong>" : $i) . "</a>\n";
	$out .= '</p>'.n;
};
break;










/*----- DEFAULT -----*/





default: 
 $out = "<h1>{$translate['error']} 404</h1>
 <p>{$translate['wrongreq']}</p>";
 header("HTTP/1.0 404 Not Found");
break;};