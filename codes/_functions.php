<?php
if (!defined ('in')) exit ();










/*----- Zakladne -----*/





// Meno autora (green)
function GetAuthorName ($id) {
	global $_CONFIG, $prefix;
	if ($info = @mysql_fetch_assoc (@mysql_query ("SELECT `name`, `isadmin` FROM `{$prefix}_moderators` WHERE `id` = $id LIMIT 1")))
	return '<span class="' . (($info['isadmin'] == 1) ? 'siteAuthor' : 'siteModerator') . '">' . $info['name'] . '</span>';
	else return '<span class="siteAuthor">' . $_CONFIG['author'] . '</span>';
};





// Twitter tlačidlo
function twitterButton ($url = null, $title = null) {
	global $_CONFIG, $tohead;
	if (empty ($_CONFIG['twitterbutton'])) return null;
	$params = array (
		'href'		=> 'http://twitter.com/share',
		'class'		=> 'twitter-share-button',
		'data-count'	=> 'horizontal',
	);
	if (!empty ($url)) $params['data-url'] = $url;
	if (!empty ($title)) $params['data-text'] = langrep ('twitterbutton', $title, $_CONFIG['title']);
	if (!empty ($_CONFIG['twittername'])) $params['data-via'] = $_CONFIG['twittername'];
	foreach ($params as $index => $param) $str[] = $index . '=' . var_export ($param, true);
	$tohead[] = '<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
	return '<a ' . implode (' ', $str) . '>Tweet</a>' . n;
}





// SeoTitle

function SeoTitle ($s) {
	$s = iconv ('UTF-8', 'WINDOWS-1250//IGNORE', $s);
	$s = strtr ($s, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e\xbf"
	. "\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7"
	. "\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef"
	. "\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe", 'ALLSSSSTZZZallssstzzzRAAAALCCCEEEEII'
	. 'DDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt');
	$s = strtolower ($s);
	$s = preg_replace ('#[^a-z0-9]+#', '-', $s);
	$s = trim ($s, '-');
	return $s;
};





// Delenie na tisícky

function oddel ($num) {
	$count = strlen($num);
	if ($count > 3) {
		for ($i = 1; $i * 3 < $count; ++$i) {
			$w = $count - (3 * $i);
			$num = substr ($num, 0, $w) . ' ' . substr ($num, $w);
		};
		return $num;
	} else return $num;
};





// Generovanie AS mailu
function getASmail ($mail) {
	return str_replace ('@', '(a)', $mail);
};





// Pridávanie príspevkov
function TexylaAdd ($txtarea) {
	global $tohead;
	@$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
	return '<textarea id="' . $txtarea . '" name="' . $txtarea . '" rows="3" cols="50"></textarea>
	<script type="text/javascript">
		options = Texyla.configurator.forum ("' . $txtarea . '");
		options.editorWidth = 350;
		options.submitButton = '.((ONLINE)?'true':'false').';
		reply = new Texyla (options);
	</script>' . n;
};





// Generovanie odkazov na kategóriu

function GetCatsList ($cat1, $cat2, $cat3) {
	global $prefix;
	$CatID = array ($cat1, $cat2, $cat3);
	foreach ($CatID as $index => $id) {
		if ($id != 0) $Cats[$index] = "id = '$id'";
	};
	$query = implode (' OR ', $Cats);
	if ($query == '') return false;
	$sql = mysql_query ("SELECT nadpis, skr, id FROM {$prefix}_cats WHERE $query AND showing = 1 ORDER BY nadpis ASC LIMIT 3");
	if (mysql_num_rows ($sql) == 0) return false;
	while ($info = @mysql_fetch_row ($sql)) $link[] = '<a href="'.rwl('kategoria', $info[2].'-'.$info[1]).'" class="articleCategory" id="cat' . $info[2] . '">'.$info[0].'</a>';
	$links = implode (', ', $link);
	return $links;
};





// ReWriteLink Creator
function rwl ($index, $value, $and = false) {
	global $_CONFIG;
	$out = ($_CONFIG['rewrite'] == 1) ? "$index-$value.html" : "?$index=$value";
	if ($and) $out .= ($_CONFIG['rewrite'] == 1) ? '?' : '&amp;';
	return $out;
};





/*--- Ošetrenie vstupných dát ---*/

function adjust($string, $int = false) {
	
	return $int ? (int)$string : mysql_real_escape_string($string);
};





/*--- Odstránenie HTML značiek ---*/

function setQ ($text) { return str_replace (array ('"', "'"), array ('&quot;', '&#39;'), $text); };





// MultiDateFormat Generator
function mdf ($input, $date) {
	global $translate;
	while (true) {
	if (false === ($pos = strpos ($input, '%'))) break;
	else {$type = substr ($input, $pos+1, 1);
	switch ($type) {
		case 'Y': $return = date ('Y', $date); break;				// rok 2008
		case 'y': $return = date ('y', $date); break;				// rok 08
		case 'm': $return = date ('m', $date); break;				// mesiac (01-12)
		case 'w': $return = date ('w', $date); break;				// den v tyzdni (1-7)
		case 'd': $return = date ('d', $date); break;				// den v mesiaci (01-31)
		case 'H': $return = date ('H', $date); break;				// hodina (0-23)
		case 'i': $return = date ('i', $date); break;				// minúta (0-59)
		case 's': $return = date ('s', $date); break;				// sekunda (0-59)
		case 'M': $return = $translate['ms' . date ('m', $date)]; break;	// skratka mesiaca
		case 'F': $return = $translate['stats.' . date ('m', $date)]; break;	// názov mesiaca
		case 'l': $return = $translate['w' . date ('w', $date)]; break;		// názov dňa
	};
	$input = str_replace ('%' . $type, $return, $input);};};
	return $input;
};





/*--- Odkazy na tágy ---*/

function getTags ($tags) {
	global $translate;
	foreach (array_filter (explode (', ', $tags)) as $value)
	$array[] = '<a href="' . rwl ('page', 'search', true) . 'tag=' . urlencode ($value) . '" class="articleTag">' . $value . '</a>';
	if (!isset ($array)) return $translate['none'];
	return implode (', ', $array);
};






/*--- Odkazy na linkovacie služby ---*/

function getLinkServicies ($link, $title) {
	global $translate;
	$serv = array (
		array ('http://friends.tatarko.sk/index.php?modul=links&amp;add', 'Opiner Friends', 'links.png'),
		array ('http://digg.com/submit?phase=2&amp;url={LINK}&amp;title={TITLE}', 'Digg', 'digg.png'),
		array ('http://del.icio.us/post?url={LINK}&amp;title={TITLE}', 'del.icio.us', 'delicious.png'),
		array ('https://favorites.live.com/quickadd.aspx?marklet=1&amp;url={LINK}&amp;title={TITLE}', 'Live', 'live.png'),
		array ('http://www.google.com/bookmarks/mark?op=edit&amp;bkmk={LINK}&amp;title={TITLE}', 'Google', 'google.png'),
		array ('http://technorati.com/faves?add={LINK}', 'Technorati', 'technorati.png'),
		array ('http://www.spurl.net/spurl.php?url={LINK}&amp;title={TITLE}', 'Spurl', 'spurl.png'),
		array ('http://vybrali.sme.sk/submit.php?url={LINK}&amp;title={TITLE}', 'Vybrali.sme.sk', 'vybrali.gif'),
		array ('http://www.mojelinky.sk/bookmarks.php?action=addaddress={LINK}&amp;title={TITLE}', 'Mojelinky.sk', 'mojelinky.png'),
		array ('javascript:window.print();', $translate['links.print'], 'printer.png'),
		array ('mailto:?subject={TITLE}&amp;body={LINK}', $translate['links.sbe'], 'email.png'),
	);
	foreach ($serv as $value)
	$array[] = '<a href="' . $value[0] . '" title="' . $value[1] . '" rel="nofollow"><img src="media/link-services/' . $value[2] . '" alt="' . $value[1] . '" /></a>';
	$array = str_replace (array ('{LINK}', '{TITLE}'), array ($link, $title), $array);
	return implode ("\n\t\t\t", $array);
};





/*--- Práca s prehliadačmi obrázkov ---*/

function loadImageBrowser ($gallery = '') {
	global $_CONFIG;
	if (!class_exists ($_CONFIG['imgbrowser'])) {
		if (@file_exists ('codes/' . $_CONFIG['imgbrowser'] . '/browser.php')) {
			include_once ('codes/' . $_CONFIG['imgbrowser'] . '/browser.php');
			if (!class_exists ($_CONFIG['imgbrowser'])) return false;
		} else return false;
	};
	eval ('$browser = new ' . $_CONFIG['imgbrowser'] . ' ();');
	$browser -> load();
	$browser -> gallery = $gallery;
	return $browser;
};










#-------------------------------------------------
#
#  HCM PARSOVANIE
#
#-------------------------------------------------





function hcm ($co, $menuid, $author) {
	global $tohead, $prefix, $_GET, $_SERVER, $_CONFIG, $template, $cache, $translate;
	$_TEMP = ($menuid === false) ? array () : $template->config["menu$menuid"];
	$param = Explode (',', $co);
	foreach ($param as $i => $h) eval ('$param' . $i . ' = str_replace (" ", "", "' . str_replace (array ('\\', '"'), array ('\\\\', '\\"'), $h) . '");');
	if ($author != 0 and false === @mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_moderators` WHERE `id` = " . intval ($author) . " AND `ahcm` LIKE '" . adjust ($param0) . "' LIMIT 1")))
	return ''; 
	switch ($param0) {

case 'fasttext':
	$param1 = (isset($param1) and $param1 != '') ? $param1 : 175;
	$param2 = (isset($param2) and $param2 != '') ? $param2 : 250;
	$out = '<iframe src="media/fasttext.php" width="'.$param1.'" height="'.$param2.'" scrolling="auto" frameborder="0" style="border-width:0"></iframe>';
break;

case 'php':
	unset ($param[0]);
	$str = implode (',', $param);
	eval ($str);
break;

case 'include':
	if (@file_exists ($param1)) {
		include ($param1);
	};
if (!isset ($out)) $out = '';
break;

case 'plugin':
if ($plugin = OpinerAutoLoader::loadPlugin ($param1, 'hcm'))
return $plugin -> run ();
break;

default:
if (false !== ($pos = strrpos ($param0, '/'))) $param0 = substr ($param0, $pos + 1);
if (false !== ($pos = strpos ($param0, '.'))) $param0 = substr ($param0, 0, $pos);
if (@file_exists ('admin/hcm/' . $param0 . '.php')) {
	include ('admin/hcm/' . $param0 . '.php');
};
if (!isset ($out)) $out = '';
break;
}; return $out; };





/*----- HCM PARSER -----*/





function HcmParser ($co, $menuid = false, $author = false) {
	$co = str_replace (array ('<p>[hcm]', '[/hcm]</p>'), array ('[hcm]', '[/hcm]'), $co);
	while (true) {
		$start = strpos ($co, '[hcm]');
		$end = strpos ($co, '[/hcm]');
		if ($start === false or $end === false or $end <= $start) break;
		else {
			$hcm_data = substr ($co, $start+5, $end - ($start + 5));
			$co = str_replace ('[hcm]' . $hcm_data . '[/hcm]', hcm ($hcm_data, $menuid, $author), $co);
		};
	};
	return $co;
};
?>