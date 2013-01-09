<?php

/*
	- Opiner CMS
	- Powered by (vytvorilo) Ovalio
	- http://opiner.tatarko.sk/
	- Šírené pod licenciou GNU/GPL (General Public License)
	- Viac v súbore readme
	- alebo manuáli v administrácií
*/



// Prihlasovanie
session_start ();



// Definícia základných premenných
if (!file_exists ('admin/includes/default-vars.php')) exit();
include_once ('admin/includes/default-vars.php');
error_reporting(0);



// Checker
if (@file_exists ('install.php')) Header ('Location: install.php');
if (substr (PHP_VERSION, 0, 1) < 5) exit (chyba ('nie je možné spustiť stránku - verzia PHP nie je kompatibilná'));
if (!file_exists ('admin/includes/TemplateClass.php')) exit (chyba ('nie je možné načítať súbor <kbd>admin/includes/TemplateClass.php</kbd>'));
if (!file_exists ('codes/_functions.php')) exit (chyba ('nie je možné načítať súbor <kbd>codes/_functions.php</kbd>'));
include ('codes/_functions.php');
include ('admin/includes/TemplateClass.php');
include ('codes/texyla/texyla.php');



// Pripojenie k MySQL
if (!file_exists ('media/get-config.php')) exit (chyba ('nie je možné načítať súbor <kbd>media/get-config.php</kbd>'));
include ('media/get-config.php');
$title = $_CONFIG['title'];
$sep = ' ' . $_CONFIG['sep'] . ' ';



// Mobilná verzia
if (isset ($_COOKIE['mobileview']) and $_COOKIE['mobileview'] == 'false' and !isset ($_GET['mobileview'])) define('MOBILE', false);
else if (isset ($_GET['mobileview']) and $_GET['mobileview'] == 'false') {
	setcookie ('mobileview', 'false', time() + 24*3600);
	define ('MOBILE', false);
} else if (false !== stripos ($_SERVER['HTTP_USER_AGENT'], 'mobile')
or (isset ($_GET['mobileview']) and $_GET['mobileview'] == 'true')
or (isset ($_COOKIE['mobileview']) and $_COOKIE['mobileview'] == 'true')) {
	$_CONFIG['template'] = 'mobile';
	$_CONFIG['admin_foot_link'] = 0;
	$_CONFIG['facebooklike'] = 0;
	$_CONFIG['twitterbutton'] = 0;
	$_CONFIG['global_reads'] = 0;
	$_CONFIG['global_voting'] = 0;
	$_CONFIG['global_linkers'] = 0;
	$_CONFIG['list_arts'] = 5;
	$_CONFIG['list_cats'] = 5;
	$_CONFIG['menu_last_arts'] = 5;
	$_CONFIG['similararts'] = 0;
	$_CONFIG['sameoldarts'] = 0;
	setcookie ('mobileview', 'true', time() + 24*3600);
	define ('MOBILE', true);
} else define ('MOBILE', false);



// Načítanie prekladu
if (!isset ($_CONFIG['language'])) $_CONFIG['language'] = 'slovak';
if (file_exists ("languages/{$_CONFIG['language']}.php")) {
	include ("languages/{$_CONFIG['language']}.php");
} else exit (chyba ('nie je možné načítať preklad systému!'));



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
if (array_search ($_SERVER['REMOTE_ADDR'], explode ("\n", str_replace (array ("\r", "\n\n"), array ("\n", "\n"), $_CONFIG['blockIP']))) !== false)
die (header ('HTTP/1.1 403 Forbidden') . 'Forbidden');



// Nacitanie desingu
if ($template = new TemplateClass ($_CONFIG['template'])) {}
else if ($template = new TemplateClass ('Carmen')) {}
else exit (chyba ($translate['er1']));



// Ziťovanie strany, jej existencie...
$types = array (
	'clanok'	=> 'clanok',
	'kategoria'	=> 'cat',
	'galeria'	=> 'gallery',
	'sekcia'	=> 'sec',
	'page'		=> 'pages',
	'stranka'	=> 'pages',
	'gallery'	=> 'gall',
	'kniha'		=> 'book',
	'archiv'	=> 'archive'
);
foreach ($types as $index => $fil) {
	if (isset ($_REQUEST[$index]) and !empty ($_REQUEST[$index]))
	$file = "codes/$fil.php";
};
if (!isset ($file) and isset ($_REQUEST['plugin']) and !empty ($_REQUEST['plugin'])) {
	if (!class_exists ('plugin')) {
		if (@file_exists ('admin/includes/pluginClass.php')) {
			include ('admin/includes/pluginClass.php');
		} else return '';
	};
	if ($plugin = loadPlugin ($_REQUEST['plugin'], 'plugin')) {
		$title .= $sep . $plugin -> title;
		$out = '<h1 align="center">' . $plugin -> title . '</h1>'.n;
		$out .= $plugin -> run ();
		$_META['description'] = $plugin -> description;
	} else $out = "<p>{$translate['er2']}</p>\n";
} else if (!isset ($file)) {
	if ($_CONFIG['homepage'] == '#') {
		$title .= $sep . $translate['lastarts'];
		$out = HcmParser ("[hcm]arts,,{$_CONFIG['list_arts']}[/hcm]");
		$_META['keywords'] = $translate['lastarts.keys'];
		$_META['description'] = $translate['lastarts.desc'];
	} else {
		list ($id, $seo) = @mysql_fetch_row (@mysql_query ("SELECT `id`, `seo` FROM `{$prefix}_sec` WHERE `id` = {$_CONFIG['homepage']} LIMIT 1"));
		$_GET['sekcia'] = "$id-$seo";
		$file = 'codes/sec.php';
	};
};
if ((isset ($file) and @file_exists($file)) or isset ($out)) {
	if (!isset ($out)) { include($file); };
} else $out = "<h1 align=\"center\">{$translate['error']}</h1>\n<p>{$translate['wrongreq']}</p>";



// Štatistiky
if ($_CONFIG['stats'] == 1 and stripos ($_SERVER['HTTP_USER_AGENT'], array ('google', 'jyxo', 'seznam', 'slurp', 'crawler', 'bot')) === false) {
	@mysql_query ("INSERT INTO `{$prefix}_hits` VALUES ('0','{$_SERVER["REMOTE_ADDR"]}','?{$_SERVER['QUERY_STRING']}',NOW(),'{$_SERVER['HTTP_USER_AGENT']}')");
	if (!@mysql_fetch_row (mysql_query ("SELECT id FROM `{$prefix}_visits` WHERE `browser` = '{$_SERVER['HTTP_USER_AGENT']}' AND `ip` = '{$_SERVER["REMOTE_ADDR"]}' AND `kedy` >= SUBDATE(NOW(),INTERVAL 15 MINUTE)")))
	@mysql_query("INSERT INTO `{$prefix}_visits` VALUES ('0','{$_SERVER["REMOTE_ADDR"]}',NOW(),'{$_SERVER['HTTP_USER_AGENT']}')");
};



// Spracovanie menu
for ($i = 1; $i <= $template->config['info']['count-menu']; ++$i) {
	if (isset ($template->config["menu$i"])) {
		$_TEMP = $template->config["menu$i"];
		$jetobox = ($_TEMP['type'] == 'boxes') ? 1 : 0;
		$jtbs = ($jetobox == 1) ? '1 OR jetobox = 0' : '0';
		$query = @mysql_query ("SELECT text, obsah, mname FROM {$prefix}_menu WHERE kdeje = '$i' AND (jetobox = $jtbs) ORDER BY post ASC");
		if ($jetobox == 1) {
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
				};
				$template->menu["menu$i"] .= $_TEMP['menu-end'].n;
			};
		} else {
			if ($info = @mysql_fetch_row ($query)) {
				$template->menu["menu$i"] = $_TEMP['menu-start'].n;
				$template->menu["menu$i"] .= ($info[1] == '<%mainmenu%>') ? $template->MainMenu ($i) : HcmParser ($info[1], $i);
				$template->menu["menu$i"] .= $_TEMP['menu-end'].n;
			};
		};
	};
};



// Statické pluginy
$sql = @mysql_query ("SELECT `fname` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `static` = 1");
if (mysql_num_rows ($sql) != 0)	{
	if (!class_exists ('plugin')) {
		if (@file_exists ('admin/includes/pluginClass.php')) {
			include ('admin/includes/pluginClass.php');
		} else return 'Missing /admin/includes/pluginClass.php';
	};
	while ($row = @mysql_fetch_assoc ($sql)) {
		if ($plugin = loadPlugin ($row['fname'], 'staticrun'))
		$plugin -> run ();
	};
};



// Ukončenie aplikácie
$template -> EchoTemplate ();
mysql_close();
@file_put_contents ('store/cache/cache.php', "<?php\n\$cache = " . var_export ($cache, true) . ";\n?>");
?>