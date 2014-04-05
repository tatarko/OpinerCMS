<?php

require_once 'admin/includes/System.php';

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
}

if(!file_exists('codes/_functions.php')) {

	throw new Exception('nie je možné načítať súbor <kbd>codes/_functions.php</kbd>');
}

require_once 'codes/_functions.php';


// Pripojenie k MySQL
if (!file_exists ('media/get-config.php')) throw new Exception('nie je možné načítať súbor <kbd>media/get-config.php</kbd>');
include ('media/get-config.php');
$title = $_CONFIG['title'];
$sep = ' ' . $_CONFIG['sep'] . ' ';



// Načítanie prekladu
if (!isset ($_CONFIG['language'])) $_CONFIG['language'] = 'slovak';
if (file_exists ("languages/{$_CONFIG['language']}.php")) {
	include ("languages/{$_CONFIG['language']}.php");
} else throw new Exception('nie je možné načítať preklad systému!');



// Načítanie cache hodnôt, META tags
if (@file_exists ('store/cache/cache.php')) {
	include ('store/cache/cache.php');
} else $cache = array ();
$_META = array ();



// Prihlasovanie
if ((isset ($_SESSION['user']) and false !== ($_USER_INFO = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `id` = '{$_SESSION['user']}' AND `blocked` = 0 LIMIT 1;")))) or $_USER_INFO = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `statichash` = '" . md5 (sha1 ($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])) . "' AND `blocked` = 0 LIMIT 1;"))) {
	define ('ONLINE', true);
	define ('USERTYPE', $_USER_INFO['isadmin'] ? 1 : 2);
	define ('USER', $_USER_INFO['id']);
	define ('ADMIN', $_USER_INFO['isadmin'] ? true : false);
	$_SESSION['user'] = USER;
} else define ('ONLINE', false); 



// Blokované IP
if (array_search ($_SERVER['REMOTE_ADDR'], explode ("\n", str_replace (array ("\r", "\n\n"), array ("\n", "\n"), $_CONFIG['blockIP']))) !== false) {

	throw new Fertu\HttpException(403);
}

// Nacitanie routra a templatu
System::app()->createTemplate('presto'); // !!!ACCORDINGTOCONFIG!!!
System::app()->template->layout = 'wide'; // !!!ACCORDINGTOCONFIG!!!
System::app()->createRouter('old'); // !!!ACCORDINGTOCONFIG!!!

// Štatistiky
if($_CONFIG['stats']
		&& !preg_match('#(google|jyxo|seznam|slurp|crawler|bot)#', strtolower($_SERVER['HTTP_USER_AGENT']))) {

	@mysql_query ("INSERT INTO `{$prefix}_hits` VALUES ('0','{$_SERVER["REMOTE_ADDR"]}','?{$_SERVER['QUERY_STRING']}',NOW(),'{$_SERVER['HTTP_USER_AGENT']}')");
	if (!@mysql_fetch_row (mysql_query ("SELECT id FROM `{$prefix}_visits` WHERE `browser` = '{$_SERVER['HTTP_USER_AGENT']}' AND `ip` = '{$_SERVER["REMOTE_ADDR"]}' AND `kedy` >= SUBDATE(NOW(),INTERVAL 15 MINUTE)")))
	@mysql_query("INSERT INTO `{$prefix}_visits` VALUES ('0','{$_SERVER["REMOTE_ADDR"]}',NOW(),'{$_SERVER['HTTP_USER_AGENT']}')");
};



// Spracovanie menu
$i = 0;
foreach(System::app()->template->layout->menus as $menu) {

	++$i;
	$jetobox = $menu->type != 'bar';
	$jtbs = ($jetobox == 1) ? '1 OR jetobox = 0' : '0';
	$query = @mysql_query ("SELECT text, obsah, mname FROM {$prefix}_menu WHERE kdeje = '$i' AND (jetobox = $jtbs) ORDER BY post ASC");
	if ($jetobox == 1) {
		exit('aaa');
		if (mysql_num_rows ($query) > 0) {
			$template->menu["menu$i"] = $_TEMP['menu-start'].n;
			while ($info = @mysql_fetch_row ($query)) {
				$boxid = ($info[2] == '') ? '' : ' id="menu' . $i . '_' . $info[2] . '"';
				$template->menu["menu$i"] .= str_replace ('%ID%', $boxid, $_TEMP['box-start'].n);
				$template->menu["menu$i"] .= str_replace ('%TITLE%', $info[0], $_TEMP['box-head']).n;
				if ($info[1] == '<%mainmenu%>') $template->menu["menu$i"] .= $template->MainMenu ($i);
				else if ($info[1] == '<%search%>') $template->menu["menu$i"] .= $template->GetSearchBox ($i);
				else $template->menu["menu$i"] .= HcmParser ($info[1], $i);
				$template->menu["menu$i"] .= $_TEMP['box-end'].n;
			}
			$template->menu["menu$i"] .= $_TEMP['menu-end'].n;
		}
	}
	else {
		if($info = @mysql_fetch_row ($query)) {
			System::app()->template->values['menu' . $i]['title'] = $info[0];
			System::parseMenuBox($info[1], System::app()->template->values['menu' . $i]);
		}
	}
}



// Statické pluginy
$sql = @mysql_query ("SELECT `fname` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `static` = 1");
if (mysql_num_rows ($sql) != 0)	{
	while ($row = @mysql_fetch_assoc ($sql)) {
		if ($plugin = OpinerAutoLoader::loadPlugin ($row['fname'], 'staticrun'))
		$plugin -> run ();
	};
};



// Ukončenie aplikácie
System::app()->run();

mysql_close();
@file_put_contents ('store/cache/cache.php', "<?php\n\$cache = " . var_export ($cache, true) . ";\n?>");
?>