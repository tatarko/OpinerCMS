<?php

/*
	- Opiner CMS (Ovládací Panel)
	- Powered by Ovalio
	- http://opiner.tatarko.sk/
	- Šírené pod licenciou GNU / General Public License
*/

session_start ();

if($_SERVER['HTTP_HOST'] != 'localhost') {

	error_reporting(0);
}

if(@file_exists('install.php')) {

	Header('Location: install.php');
	exit();
}

if(version_compare(PHP_VERSION, '5.3', '<')) {

	throw new Exception('nie je možné spustiť stránku - verzia PHP nie je kompatibilná');
	exit();
}

require_once 'admin/includes/default-vars.php';



// Pripojenie k MySQL
if (!file_exists ('media/get-config.php')) throw new Exception('Súbor media/get-config.php nenájdený!');
include ('media/get-config.php');



// Načítanie prekladu
if (!isset ($_CONFIG['language'])) $_CONFIG['language'] = 'slovak';
if (file_exists ("languages/{$_CONFIG['language']}.php")) {
	include ("languages/{$_CONFIG['language']}.php");
} else throw new Exception('nie je možné načítať preklad systému!'); 



// Načítanie cache hodnôt
if (@file_exists ('store/cache/cache.php')) {
	include ('store/cache/cache.php');
} else $cache = array ();



// Prihlasovanie
if ((isset ($_SESSION['user']) and false !== ($_USER_INFO = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `id` = '{$_SESSION['user']}' AND `blocked` = 0 LIMIT 1;")))) or $_USER_INFO = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `statichash` = '" . md5 (sha1 ($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])) . "' AND `blocked` = 0 LIMIT 1;"))) {
	define ('ONLINE', true);
	define ('USERTYPE', $_USER_INFO['isadmin'] ? 1 : 2);
	define ('USER', $_USER_INFO['id']);
	define ('ADMIN', $_USER_INFO['isadmin'] ? true : false);
	define ('FILTER', $_USER_INFO['isadmin'] ? '' : ' AND `autor` = ' . USER);
	define ('NAME', $_USER_INFO['name']);
	$_SESSION['user'] = USER;
} else Header ('Location: login.php');
$sql = mysql_query("SELECT `what` FROM `{$prefix}_iplog` WHERE `ip` = 'managesection' AND `hodnota` = " . USER);
while ($data = mysql_fetch_assoc ($sql))
$allowedsections[] = '`id` = ' . $data['what'];



// Kontrola premennych
if (!file_exists ('admin/includes/default-functions.php')) throw new Exception('Súbor admin/includes/default-functions.php nenájdený!');
include ('admin/includes/default-functions.php');
unset ($_GET, $_POST);
foreach ($_REQUEST as $index => $value)
$_REQUEST[$index] = str_replace (array ("\\'", '\\"', '\\\\'), array ("'", '"', '\\'), $value);
$_GET = $_REQUEST;
$_POST = $_REQUEST;
define ('ajaxmode', isset ($_REQUEST['ajaxmode']) ? true : false);



// Načítanie samotného obsahu
if (((isset ($_GET['what']) and !empty ($_GET['what']) and $what = $_GET['what']) or (!isset ($_GET ['app']) and $what = 'home'))
and @file_exists ("admin/require/$what.php") and (ADMIN or array_search ($what, array (isset($allowedsections)?'sections':null,$_USER_INFO['albums']?'gallery':NULL, $_USER_INFO['articles']?'articles':NULL, $_USER_INFO['plugins']?'apps':NULL, 'home', 'polls', 'manual', 'microblog', 'account')) !== false)) {

	// Zakladne premenne
	$pag = (!isset ($_GET['pag'])) ? 1 : $_GET['pag'];
	$limit = $_CONFIG['list_admin'];
	$out = '';
	
	// Bolo to uložené?
	if (isset ($_SERVER['HTTP_REFERER'])) {
		$qs = explode ('?', $_SERVER['HTTP_REFERER']);
		if (isset ($qs[1])) {
			$qs = explode ('&', $qs[1]);
			foreach ($qs as $q) {
				$q = explode ('=', $q);
				if (isset ($q[1])) $qs[$q[0]] = $q[1];
			};
			if (isset ($_GET['mod'], $qs['mod']) and $qs['mod'] == 'add' and $_GET['mod'] == 'edit')
			$out .= getIcon ('info', $translate['successadd']);
		};
	};
	
	// Rôzne kontroly & návrhy
	if (ADMIN) {
		if (@file_exists ('updater.php') and $what != 'updater')
		$out .= getIcon ('question', langrep ('updateq', '<a href="?what=updater">', '</a>'));
	
		if (array_search ($what, array ('confirm', 'articles')) === false and false !== ($concount = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM `{$prefix}_clanky` WHERE `confirmed` = 0")))	and $concount[0] != 0)
		$out .= getIcon ('warning', langrep ('toconfirm', $concount[0], '<a href="admin.php?what=confirm">' . $translate['redactors.confirm'] . '</a>'));

		foreach (System::app()->labsProjects as $index => $value) {
			if (!isset ($_CONFIG['labs.' . $value]) and $what != 'labs')
			$out .= getIcon ('suggestion', '<form action="admin.php?what=labs" method="post">
			' . $translate['labs.' . $index . '.sugg'] . '<br />
			<input type="submit" name="labs' . $value . '" value="' . $translate['yes'] . '" />
			<input type="submit" name="labs' . $value . '" value="' . $translate['no'] . '" />
			</form>');
		};
	};

	// Vykonanie skriptu
	include_once ('admin/require/' . $what . '.php');
} else if (isset ($_GET['app']) and !empty ($_GET['app'])) {
	$hash = adjust ($_GET['app']);
	if ($_CONFIG['pluginsAllowed'] == 0) $out = "<p>{$translate['apps.notallowed']}</p>\n";
	else if (false === ($app = @mysql_fetch_assoc (@mysql_query ("SELECT `fname`, `allowed`, `redactors`, `application` FROM `{$prefix}_apps` WHERE SHA1(CONCAT(`id`, `fname`, `installed`)) = '$hash' LIMIT 1")))) $out = '<p>Aplikácia sa nenašla!</p>';
	else if ($app['allowed'] == 0 or (!ADMIN and $app['redactors'] == 0)) $out = "<p>{$translate['apps.noperms']}</p>\n";
	else if ($app['application'] == 0) $out = "<p>{$translate['apps.nofull']}</p>\n";
	else {
		if ($plugin = OpinerAutoLoader::loadPlugin ($app['fname'], 'application')) {
			$out = HeadIfPost ($plugin -> title);
			$out .= $plugin -> run ();
		} else $out = "<p>{$translate['apps.loadingerror']}</p>\n";
	};
} else $out = "<p>{$translate['wrongreq']}</p>\n";



// Spustiť pluginy/widgety
if (!ajaxmode){
if (isset ($_REQUEST['refresh']) and strlen ($_REQUEST['refresh']) == 40 and ADMIN) @mysql_query ("UPDATE `{$prefix}_apps` SET `cached` = 0 WHERE SHA1(CONCAT(`id`, `fname`, `installed`)) = '" . adjust ($_REQUEST['refresh']) . "' LIMIT 1");
$sql = @mysql_query ("SELECT `id`, `fname`, `title`, `cache`, `cached` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `widget` = 1" . ((ADMIN)?'':' AND `redactors` = 1') . " ORDER BY `position` ASC");
if ($_CONFIG['pluginsAllowed'] and (ADMIN or $_USER_INFO['plugins']) and mysql_num_rows ($sql) > 0) {
	while ($widget = @mysql_fetch_assoc ($sql)) {
		if ($widget['cache'] == 1 and isset ($cache['widget_' . USERTYPE . $widget['fname']]) and $widget['cached'] > time() - 604800) @$pl .= $cache['widget_' . USERTYPE . $widget['fname']];
		else if ($app = OpinerAutoLoader::loadPlugin ($widget['fname'], 'widget')) {
			$fullscreen = ($app -> canRun ('application')) ? ' <a href="admin.php?app=' . $app -> apphash . '" title="' . $translate['apps.full.title'] . '"><img src="admin/images/icon-fullscreen.png" /></a>' : '';
			$settings = (ADMIN) ? ' <a href="?what=market&settings=' . $widget['id'] . '" title="' . $translate['apps.settings.title'] . '"><img src="admin/images/mSettings.png" /></a>' : '';
			$refresh = (ADMIN and $widget['cache'] == 1) ? ' <a href="admin.php?refresh=' . $app -> apphash . '" title="' . $translate['apps.dropcache.title'] . '"><img src="admin/images/icon-move.png" /></a>' : '';
			$return = ' <div class="sidebarPlugin"><h2>' . $widget['title'] . '<span class="icons">' . $settings . $refresh . $fullscreen . "</span></h2>\n<div class=\"inner\">\n" . $app -> run () . n . "</div>\n</div>\n";
			if ($widget['cache'] == 1) {
				$cache['widget_' . USERTYPE . $widget['fname']] = $return;
				@mysql_query ("UPDATE `{$prefix}_apps` SET `cached` = UNIX_TIMESTAMP() WHERE `id` = {$widget['id']} LIMIT 1");
			};
			@$pl .= $return;
		};
	};
	if (isset ($pl)) {
		$tohead[] = '<script src="admin/remote/js/widgets.js" type="text/javascript"></script>';
		$tohead[] = '<link rel="stylesheet" href="admin/remote/css/widgets.css" type="text/css" />';
		$pl = '<div id="plugins">' . n . $pl . '</div>';
	} else $pl = '';
} else $pl = '';} else $pl = '';



// Načítanie motívu farieb
if ($_CONFIG['admincolor'] != 'default' and !ajaxmode)
$tohead[] = '<link rel="stylesheet" href="admin/remote/schemas/' . $_CONFIG['admincolor'] . '.theme.php" type="text/css" />';




// Hlavicka
$tohead = array_unique ($tohead);
$tohead = implode ("\n ", $tohead);


// OutPut
if (!isset ($out)) $out = '<h1 align="center">' . $translate['error'] . '</h1>
<p>' . $translate['noreturn'] . '</p>';



// Strings To Desing
$MAINHEADING = (isset ($MAINHEADING)) ? ' &raquo <a href="' . _SiteForm . '">' . $MAINHEADING . '</a>' : '';
$info = explode ('~$~', SystemInfo);
include ('admin/remote/template.php');
@file_put_contents ('store/cache/cache.php', "<?php\n\$cache = " . var_export ($cache, true) . ";\n?>");
@mysql_close();
?>