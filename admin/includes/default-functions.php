<?php
if (!defined ('in')) exit ();



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



// ReWriteLink Creator
function rwl ($index, $value, $and = false) {
	global $_CONFIG;
	$out = ($_CONFIG['rewrite'] == 1) ? "$index-$value.html" : "./?$index=$value";
	if ($and) $out .= ($_CONFIG['rewrite'] == 1) ? '?' : '&amp;';
	return $out;
};



// Generovanie oznámenia
function GetIcon ($name, $text) {
	$out = '<div class="infobox">'.n;
	$out .= '<img src="admin/images/notice-' . $name . '.png" />'.n;
	$out .= '<p>' . $text . '</p>'.n;
	$out .= '</div>'.n;
	return $out;
};



// Výpis motívov
function GetTemplateList ($actual) {
	$return = '<select name="template">';
	$dir = opendir ('templates/');
	while ($file = readdir ($dir)) {
		if ($file != '.' and $file != '..' and $file != 'mobile' and is_dir('templates/' . $file . '/')
		and file_exists ("templates/$file/body.php") and file_exists ("templates/$file/config.php")
		and file_exists ("templates/$file/style.css")) {
			$return .= ($file == $actual)?
			"<option value=\"$file\" selected=\"selected\">$file</option>":
			"<option value=\"$file\">$file</option>";
		};
	};
	closedir ($dir);
	return $return . '</select>';
};



// Výpis prekladov
function GetLangList ($actual) {
	$return = '<select name="language">';
	$dir = opendir ('languages/');
	while ($file = readdir ($dir)) {
		if ($file != '.' and $file != '..' and substr ($file, -3) == 'php') {
			include ('languages/' . $file);
			$name = substr ($file, 0, -4);
			$return .= ($file == $actual)?
			'<option value="' . $name . '" selected="selected">' . $translate['info.name'] . ' (' . $translate['info.iname'] . ')</option>':
			'<option value="' . $name . '">' . $translate['info.name'] . ' (' . $translate['info.iname'] . ')</option>';
		};
	};
	closedir ($dir);
	return $return . '</select>';
};



// Výpis wysiwygov
function GetWysiwygList ($actual) {
	$return = '<select name="wysiwyg">';
	$dir = opendir ('codes/');
	while ($file = readdir ($dir)) {
		if ($file != '.' and $file != '..' and is_dir('codes/' . $file . '/') and file_exists ("codes/$file/$file-control.php")) {
			include ("codes/$file/$file-control.php");
			$return .= ($file == $actual)?
			"<option value=\"$file\" selected=\"selected\">{$WysiwygInfo['name']} {$WysiwygInfo['version']}</option>":
			"<option value=\"$file\">{$WysiwygInfo['name']} {$WysiwygInfo['version']}</option>";
		};
	};
	closedir ($dir);
	return $return . '</select>';
};



// Výpis prehliadačov
function GetBrowserList ($actual) {
	$return = '<select name="imgbrowser">';
	$dir = opendir ('codes/');
	while ($file = readdir ($dir)) {
		if ($file != '.' and $file != '..' and is_dir('codes/' . $file . '/') and file_exists ("codes/$file/browser.php")) {
			include ("codes/$file/browser.php");
			eval ('$object = new ' . $file . ' ();');
			$return .= ($file == $actual) ? '<option value="' . $file . '" selected="selected">' . $object -> name . ' ' . $object -> version . '</option>' : '<option value="' . $file . '">' . $object -> name . ' ' . $object -> version . '</option>';
		};
	};
	closedir ($dir);
	return $return . '</select>';
};



// Hlavička (ak je post)
function HeadIfPost ($text) {
	global $out, $MAINHEADING;
	$MAINHEADING = $text;
	return $out;
};



// Ikonka
function TempIcon ($name) {
	if (file_exists ('admin/images/icon-' . $name . '.png'))
	return '<img src="admin/images/icon-' . $name . '.png" border="0" class="addit" />';
	else return '';
}



// Strankovanie
function GetPagesList ($table, $link, $where = '') {
	global $limit, $pag, $prefix, $translate;
	$query = "SELECT COUNT(*) FROM {$prefix}_$table";
	if ($where !== false and $where != '') $query .= ' WHERE ' . $where;
	$kolko = mysql_fetch_row (mysql_query ($query));
	if($kolko[0] > $limit) {
		$out = '<p align="center"><strong>' . $translate['paging'] . '</strong><br />' . n;
		for ($i = 1; $kolko[0] - ($limit * $i) > 0; ++$i) {
			$out .= '<a href="?what=' . $link . '&pag=' . $i . '" title="' . langrep ('gopage', $i) . '">';
			$out .= ($i ==  $pag) ? '<strong>[' . $i . ']</strong></a>' . n :  $i . '</a>' . n;};
		if ($kolko[0] - ($limit * ($i - 1)) + 1 > 0 and $i != 1) {
			$out .= '<a href="?what=' . $link . '&pag=' . $i . '" title="' . langrep ('gopage', $i) . '">';
			$out .= ($i ==  $pag) ? '<strong>[' . $i . ']</strong></a>' . n :  $i . '</a>' . n;};
	} else $out = '';
	$out .= '</p>';
	return $out;
}



// MySQL injection protection
function GetIntFromGetValue ($int) {
	$count = strlen ($int);
	$letters = array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
	for ($i = 0; $i < $count; ++$i) {
		$letter = substr ($int, $i, 1);
		if (array_search ($letter, $letters) !== false) @$output .= $letter;
	}
	if (!isset ($output)) $output = 0;
	return $output;
}



// Milion Number
function GetMilionNumber ($number) {
	for ($len = strlen ($number); $len < 7; ++$len) $number = '0' . $number;
	return $number;
};



// ImageType
function GalleryType ($id) {
	global $prefix;
	$type = mysql_fetch_row (mysql_query ("SELECT `type` FROM `{$prefix}_img` WHERE `id` = '$id' LIMIT 1"));
	return $type[0];
};




// Generovanie odkazov na kategóriu
function GetCatsList ($cat1, $cat2, $cat3) {
	global $prefix;
	$CatID = array ($cat1, $cat2, $cat3);
	foreach ($CatID as $index => $id) {
		if ($id != 0) $Cats[$index] = "`id` = '$id'";
	};
	$query = implode (' OR ', $Cats);
	if ($query == '') return false;
	$sql = mysql_query ("SELECT `nadpis`, `id` FROM `{$prefix}_cats` WHERE $query AND `showing` = 1 ORDER BY `nadpis` ASC LIMIT 3");
	if (mysql_num_rows ($sql) == 0) return false;
	while ($info = @mysql_fetch_row ($sql)) $link[] = '<a href="admin.php?what=articles&cat=' . $info[1] . '">'.$info[0].'</a>';
	$links = implode (', ', $link);
	return $links;
};





/*--- Ošetrenie vstupných dát ---*/

function adjust ($string, $int = false) {
	if ($int === true) return ($string + 0);
	else if (!is_numeric ($string)) {
		if (@function_exists ('mysql_real_escape_string')) return mysql_real_escape_string ($string);
		else if (@function_exists ('mysql_escape_string')) return mysql_escape_string ($string);
		else return addslashes ($string);
	} else return $string;
};





/*--- Ukladanie zmein nastavení ---*/

function ConfigUpdate ($index, $value) {
	global $prefix, $_CONFIG;
	if (@mysql_query ("UPDATE `{$prefix}_config` SET `hodnota` = '".adjust($value)."' WHERE `nazov` = '$index' LIMIT 1")) {
		$_CONFIG[$index] = $value;
		return true;
	} else return false;
};





/*--- Odstránenie HTML značiek ---*/

function setQ ($text) { return str_replace (array ('"', "'"), array ('&quot;', '&#39;'), $text); };





/*----- Ikonka Priečinka / Súboru -----*/

function FileManagerIcon ($type, $file_src = '') {
	switch ($type) {
		case 'folder': $icon = 'admin/opiner-text/images/mime/folder.png'; break;
		case 'xls':
		case 'xlsx':
		case 'xlt':
		case 'xlv':
		case 'xlw': $icon = 'admin/opiner-text/images/mime/excel.png'; break;
		case 'mp3':
		case 'mp4':
		case 'cda':
		case 'wav':
		case 'ogg': $icon = 'admin/opiner-text/images/mime/sound.png'; break;
		case 'pdf': $icon = 'admin/opiner-text/images/mime/pdf.png'; break;
		case 'gt':
		case 'moov':
		case 'mov': $icon = 'admin/opiner-text/images/mime/quicktime.png'; break;
		case 'zip':
		case 'rar':
		case 'tar':
		case 'gz':
		case 'tgz': $icon = 'admin/opiner-text/images/mime/archive.png'; break;
		case 'list':
		case 'log':
		case 'text':
		case 'txt':
		case 'c':
		case 'c++':
		case 'com':
		case 'conf':
		case 'java':
		case 'jav': $icon = 'admin/opiner-text/images/mime/text.png'; break;
		case 'm1v':
		case 'm2v':
		case 'movie':
		case 'mp2':
		case 'mpeg':
		case 'mpg':
		case 'avi':
		case 'wmv':
		case '3gp': $icon = 'admin/opiner-text/images/mime/video.png'; break;
		case 'inc':
		case 'phtml':
		case 'php3':
		case 'php5':
		case 'php': $icon = 'admin/opiner-text/images/mime/php.png'; break;
		case 'js': $icon = 'admin/opiner-text/images/mime/js.png'; break;
		case 'css': $icon = 'admin/opiner-text/images/mime/css.png'; break;
		case 'html':
		case 'htm': $icon = 'admin/opiner-text/images/mime/html.png'; break;
		case 'jpg':
		case 'jpeg':
		case 'gif':
		case 'png':
		case 'bmp': $icon = $file_src; break;
		case 'aps': $icon = 'admin/opiner-text/images/mime/image.png'; break; 
		case 'xml': $icon = 'admin/opiner-text/images/mime/xml.png'; break; 
		default: return false; break;
	};
	return '<img src="' . $icon . '" style="border:0;max-width:96px;max-height:96px;" alt="" />';
};





/*----- Môžem Editovať? -----*/

function FileManagerCanOpen ($type) {
	switch ($type) {
		case 'xml':
		case 'list':
		case 'log':
		case 'text':
		case 'txt':
		case 'c':
		case 'c++':
		case 'com':
		case 'conf':
		case 'java':
		case 'jav':
		case 'phtml':
		case 'php3':
		case 'php5':
		case 'php':
		case 'css':
		case 'html':
		case 'htm': return true; break;
		default: return false; break;
	};
};





/*----- Filtrovanie výsledkov -----*/

function GetFilterQ ($and = false, $row = 'nadpis') {
	$return = ($and === true) ? ' AND ' : ' WHERE ';
	if (isset ($_SESSION['filter']) and strlen ($_SESSION['filter']) >= 1) {
		$return .= "(`$row` LIKE '%{$_SESSION['filter']}%' OR `$row` LIKE '%";
		$return .= strtoupper ($_SESSION['filter']) . "%' OR `$row` LIKE '%";
		$return .= strtoupper (substr ($_SESSION['filter'], 0, 1)) . substr ($_SESSION['filter'], 1) . "%')";
	} else return '';
	return $return;
};





/*----- Ošetrenie tágov -----*/

function adjustTags ($tags) {
	$tags = explode (',', $tags);
	foreach ($tags as $index => $value) {
		while (substr ($value, 0, 1) == ' ') $value = substr ($value, 1);
		while (substr ($value, -1, 1) == ' ') $value = substr ($value, 0, -1);
		$tags[$index] = $value;
	};
	return implode (', ', $tags);
};





/*--- Načítanie súboru s doplnkovým prekladom ---*/

function loadLang ($name) {
	global $_CONFIG, $translate;
	if (file_exists ("languages/{$_CONFIG['language']}/$name.php")) {
		include ("languages/{$_CONFIG['language']}/$name.php");
		return true;
	} else return false;
}





/*--- Pridá priečinok do ZIP archívu ---*/

function addDir2Zip ($path, $zip, $subdirs = false) {
        $dir = opendir ($path);
	$zip -> addEmptyDir ($path);
	while ($file = readdir ($dir)) {
		if ($file != '.' and $file != '..') {
			if (is_dir ($path . '/' . $file)) {
				if ($subdirs === true)
				addDir2Zip ($path . '/' . $file, $zip, true);
			} else $zip -> addFile ($path . '/' . $file, $path . '/' . $file);
		};
	};
	closedir ($dir);
}





/*--- Odinštaluje celý systém ---*/

function uninstallSystem ($tables) {
	mysql_query ('DROP TABLE IF EXISTS `' . implode ('`, `', $tables) . '`');
	dropPath ('./');
}





/*--- Zmaž priečinok vrátane pod-priečinkov ---*/

function dropPath ($path) {
	$dir = opendir ($path);
	while ($file = readdir ($dir))
	if ($file != '.' and $file != '..')
	if (is_dir ($path . $file)) {
		dropPath ($path . $file . '/');
                @rmdir($path . $file);
        } else @unlink ($path . $file);
	return true;
}





/*--- Zisti meno autora ---*/

function GetAuthorName ($id) {
	global $_CONFIG, $prefix, $cache, $translate;
	if (isset ($cache ['authors'] [$id]))
		return $cache ['authors'] [$id];

	if ($info = @mysql_fetch_assoc (@mysql_query ("SELECT `name` FROM `{$prefix}_moderators` WHERE `id` = $id LIMIT 1")))
		$cache ['authors'] [$id] = $info['name'];
		else $cache ['authors'] [$id] = $translate ['redactors.noExists'];
	return $cache ['authors'] [$id];
};
?>