<?php

// Štandardné Záležitosti
session_start ();
if (!file_exists ('../admin/includes/default-vars.php')) exit ();
include_once ('../admin/includes/default-vars.php');
if (!file_exists ('get-config.php')) exit ();
include_once ('get-config.php');



// Načítanie prekladu
if (file_exists ("../languages/{$_CONFIG['language']}.php")) {
	include ("../languages/{$_CONFIG['language']}.php");
} else exit (chyba ('nie je možné načítať preklad systému!'));




// Ošetrenie vstupných dát
function adjust ($string, $int = false) {
	if ($int === true) return ($string + 0);
	else if (!is_numeric ($string)) {
		if (@function_exists ('mysql_real_escape_string')) return mysql_real_escape_string ($string);
		else if (@function_exists ('mysql_escape_string')) return mysql_escape_string ($string);
		else return addslashes ($string);
	} else return $string;
};



// Auto. Prihlasovanie
if ((isset ($_SESSION['user']) and $_SESSION['user'] == $_CONFIG['loginpass']) or md5 (sha1 ($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])) == $_CONFIG['staticlogin']) {
	define ('ONLINE', true);
	define ('USERTYPE', 1);
	define ('USER', 0);
	define ('ADMIN', true);
	define ('NAME', $_CONFIG['author']);
} else if ((isset ($_SESSION['user']) and false !== ($_USER_INFO = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `id` = '{$_SESSION['user']}' AND `blocked` = 0 LIMIT 1;")))) or $_USER_INFO = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `statichash` = '" . md5 (sha1 ($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])) . "' AND `blocked` = 0 LIMIT 1;"))) {
	define ('ONLINE', true);
	define ('USERTYPE', 2);
	define ('USER', $info['id']);
	define ('ADMIN', false);
	define ('NAME', $_USER_INFO['name']);
} else define ('ONLINE', false); 



// Kde Sme?
$mode = (isset ($_REQUEST['mode']) and array_search ($_REQUEST['mode'], array ('home', 'add', 'edit', 'delete')) !== false) ? $_REQUEST['mode'] : 'home';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="sk">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?php if ($mode == 'home') echo '<meta http-equiv="Refresh" name="Expires" content="15">'."\n";?>
<meta name="robots" content="noindex,nofollow" />
<link rel="stylesheet" href="styling.php?what=fasttext" type="text/css" />
<title><?php echo $translate['menu.fasttext'];?></title>
</head>
<body>
<?php



/*--- Zoznam Príspevkov ---*/

if ($mode == 'home') {
	echo '<p class="link"><a href="?mode=add">' . $translate['add'] . '</a></p>'."\n";
	$id = 0;
	$query = @mysql_query ("SELECT `autor`, DATE_FORMAT(`added`, '%H:%i#%d.%m.%Y'), `text`, `id` FROM `{$prefix}_comments` WHERE `kde` = 'fasttext' ORDER BY `id` DESC LIMIT 15");
	while ($info = mysql_fetch_row ($query)) {
		$out = (++$id % 2 == 0) ? '<div class="text1">'."\n" : '<div class="text2">'."\n";
		$date = explode ('#', $info[1]);
		$out .= '<acronym style="border:none;" title="Pridané dňa '.$date[1].'"><b>('.$date[0].') '.$info[0].'</b>:<br />'.htmlspecialchars ($info[2], ENT_QUOTES).'</acronym>'."\n";
		if (ONLINE) $out .= '(<a href="?mode=delete&id='.$info[3].'">' . $translate['drop'] . '</a>)'."\n";
		$out .= '</div>'."\n";
		echo $out;
	};



/*--- Pridávanie príspevku ---*/

} else if ($mode == 'add' and !isset ($_POST['ok'])) {
	echo '<p class="link"><a href="?mode=home">' . $translate['back'] . '</a></p>'."\n";
	echo '<form action="?mode=add" method="post">'."\n";
	if (isset ($_COOKIE['comments-default-values'])) {
		$params = explode ('~$~', $_COOKIE['comments-default-values']);
		$meno = 'value="'.$params[0].'" ';
	} else $meno = '';
	if (ONLINE === false) {echo '<b>' . $translate['ft.name'] . ':</b><br /><input type="text" name="meno" size="15" '.$meno.'/><br />'."\n";};
	echo '<b>' . $translate['ft.entry'] . ':</b><br /><input type="text" name="text" /><br />'."\n";
	echo '<center><input type="submit" name="ok" value="' . $translate['add'] . '" /></center>'."\n";
	echo "</form>\n";



/*--- Pridávanie príspevku ---*/

} else if ($mode == 'add' and isset ($_POST['ok'])) {
	if (ONLINE) $meno = '<u>' . NAME . '</u>';
	else $meno = htmlspecialchars (strip_tags ($_POST['meno']), ENT_QUOTES);
	@mysql_query ("INSERT INTO `{$prefix}_comments` VALUES (0, 'fasttext', '" . adjust ($meno) . "', NOW(), '" . adjust ($_POST['text']) ."', '{$_SERVER['REMOTE_ADDR']}', '', '');");
	Header ('Location: ?mode=home');



/*--- Mazanie príspevku ---*/

} else if ($mode == 'delete' and isset ($_GET['id']) and ONLINE) {
	@mysql_query ("DELETE FROM `{$prefix}_comments` WHERE `id` = '{$_GET['id']}' LIMIT 1");
	Header ('Location: fasttext.php?mode=home');
};
?>
</body>
</html>