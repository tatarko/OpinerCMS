<?php
session_start ();
error_reporting(0);

$queries = array (
 "CREATE TABLE IF NOT EXISTS `%prefix%_apps` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `fname` tinytext NOT NULL, `title` tinytext NOT NULL, `description` text, `version` tinytext, `author` tinytext, `url` text, `friends` bigint(20) unsigned DEFAULT '0', `allowed` tinyint(1) unsigned DEFAULT '1', `homepage` tinyint(1) unsigned DEFAULT '1', `application` tinyint(1) unsigned DEFAULT '1', `widget` tinyint(1) unsigned DEFAULT '1', `hcm` tinyint(1) unsigned DEFAULT '1', `plugin` tinyint(1) unsigned DEFAULT '1', `static` tinyint(1) unsigned DEFAULT '0', `redactors` tinyint(1) unsigned DEFAULT '1', `cache` tinyint(1) unsigned DEFAULT '1', `position` int(10) unsigned DEFAULT '0', `installed` int(11) unsigned NOT NULL, `cached` int(11) unsigned DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_cats` (`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT, `nadpis` tinytext NOT NULL, `popis` text, `skr` tinytext NOT NULL, `imagelink` tinytext, `showing` tinyint(1) unsigned NOT NULL, `inmenu` tinyint(1) unsigned DEFAULT '0', `position` smallint(5) unsigned DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_clanky` (`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT, `autor` bigint(20) unsigned NOT NULL DEFAULT '0', `nadpis` tinytext NOT NULL, `seo` tinytext NOT NULL, `imagelink` tinytext, `text` mediumtext NOT NULL, `perex` mediumtext NOT NULL, `added` datetime NOT NULL, `cat` tinyint(3) unsigned NOT NULL, `cat2` tinyint(3) unsigned DEFAULT NULL, `cat3` tinyint(3) unsigned DEFAULT NULL, `showing` tinyint(1) unsigned NOT NULL, `confirmed` tinyint(1) unsigned DEFAULT '1', `coms` tinyint(1) unsigned NOT NULL, `reads` bigint(20) unsigned NOT NULL, `tags` tinytext, `album` bigint(20) unsigned DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_comments` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `kde` tinytext NOT NULL, `autor` tinytext NOT NULL, `added` datetime NOT NULL, `text` text NOT NULL, `ip` tinytext NOT NULL, `mail` tinytext, `web` tinytext, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_config` (`nazov` tinytext NOT NULL,`hodnota` text NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_download` (`id` tinyint(4) NOT NULL AUTO_INCREMENT, `hits` bigint(20) unsigned DEFAULT '0', `file` tinytext NOT NULL, `size` int(10) unsigned NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_gall` (`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT, `autor` bigint(20) unsigned NOT NULL DEFAULT '0', `nadpis` tinytext NOT NULL, `skr` tinytext NOT NULL, `showing` tinyint(1) unsigned NOT NULL, `popis` text, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_hits` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `ip` tinytext NOT NULL, `kde` tinytext NOT NULL, `kedy` datetime NOT NULL, `browser` tinytext NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_img` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `type` varchar(5) NOT NULL, `cat` smallint(5) unsigned NOT NULL, `nadpis` tinytext, `popis` text, `added` datetime NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_iplog` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `ip` tinytext NOT NULL, `what` tinytext NOT NULL, `hodnota` tinytext NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_links` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `href` tinytext NOT NULL, `title` tinytext, `target` tinytext NOT NULL, `name` tinytext NOT NULL, `location` tinyint(1) unsigned DEFAULT '0', `position` bigint(20) unsigned DEFAULT '1', PRIMARY KEY (`id`), KEY `location` (`location`,`position`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_menu` (`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT, `post` tinyint(3) unsigned NOT NULL, `jetobox` tinyint(3) unsigned NOT NULL, `kdeje` tinyint(3) unsigned NOT NULL, `text` tinytext NOT NULL, `obsah` text NOT NULL, `mname` tinytext, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_microblog` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `autor` bigint(20) unsigned NOT NULL DEFAULT '0', `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `text` text NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_moderators` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `nick` tinytext NOT NULL, `password` tinytext NOT NULL, `statichash` tinytext, `name` tinytext NOT NULL, `mail` tinytext, `isadmin` tinyint(1) unsigned DEFAULT '0', `blocked` tinyint(1) unsigned NOT NULL, `articles` tinyint(1) unsigned DEFAULT '1', `albums` tinyint(1) unsigned DEFAULT '1', `plugins` tinyint(1) unsigned DEFAULT '0', `ahcm` text, `needConfirm` tinyint(1) unsigned DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_polls` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `autor` bigint(20) unsigned NOT NULL DEFAULT '0', `question` tinytext NOT NULL, `answers` text NOT NULL, `votes` text NOT NULL, `locked` tinyint(3) unsigned NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_sec` (`id` tinyint(4) NOT NULL AUTO_INCREMENT, `nadpis` tinytext NOT NULL, `popis` tinytext, `seo` tinytext NOT NULL, `text` mediumtext NOT NULL, `com` char(1) NOT NULL, `msec` bigint(20) NOT NULL, `position` bigint(20) unsigned DEFAULT '1', PRIMARY KEY (`id`), KEY `position` (`position`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "CREATE TABLE IF NOT EXISTS `%prefix%_visits` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `ip` tinytext NOT NULL, `kedy` datetime NOT NULL, `browser` tinytext NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8;",
 "INSERT INTO `%prefix%_apps` VALUES(0, 'microblog', 'Mikroblog', 'Rýchle správy informojúce Vašich užívateľov o novinkách všeho druhu.', '1.0', 'Ovalio', 'http://opiner.tatarko.sk/', 0, 1, 0, 0, 1, 0, 0, 0, 1, 1, 2, UNIX_TIMESTAMP(), 0), (0, 'addarticle', 'Pridanie článku', 'Pridajte si túto aplikáciu medzi widgety a budete tak môcť pridávať články ešte rýchlejšie.', '1.0', 'Ovalio', 'http://opiner.tatarko.sk/', 0, 1, 0, 0, 1, 0, 0, 0, 1, 1, 1, UNIX_TIMESTAMP(), 0);",
 'INSERT INTO `%prefix%_cats` VALUES (0, "Testovacia kategória", "Prvé články na tejto stránke.", "testovacia-kategoria", NULL , 1, 1, 2);',
 'INSERT INTO `%prefix%_menu` VALUES (0, 1, 0, 1, "Hlavné menu", "<%mainmenu%>", NULL), (0, 1, 1, 2, "Ovladací panel", "<p>Pokiaľ chcete zmeniť niečo na stránke, prejdite na <a href=admin.php>Ovládací panel</a>.</p>", NULL);',
 'INSERT INTO `%prefix%_sec` VALUES (0, "Domov", "Základná sekcia vytvorená po inštalácií.", "domov", "<p>Gratulujeme, zvládli ste inštaláciu redakčného systému Opiner CMS!</p>", 0, 0, 1);',
 "INSERT INTO `%prefix%_config` VALUES
  ('admin_foot_link', '1'),
  ('ahp_plugins', '1'),
  ('antispam', '1'),
  ('author', 'Vaše meno'),
  ('autologin', '0'),
  ('blockIP', ''),
  ('conntype', '1'),
  ('desc', 'Moje pekné stránky'),
  ('facebooklike', '1'),
  ('favicon', ''),
  ('favicon_mime', ''),
  ('foot', ''),
  ('friends_mese', '1'),
  ('friends_ofhn', ''),
  ('global_author', '1'),
  ('global_comments', '1'),
  ('global_linkers', '1'),
  ('global_reads', '1'),
  ('global_voting', '1'),
  ('homepage', '1'),
  ('imgbrowser', 'pavio'),
  ('keys', 'kľúčové, slová, stránky'),
  ('language', 'slovak'),
  ('lastrandlist', '10'),
  ('list_admin', '10'),
  ('list_arts', '10'),
  ('list_cats', '10'),
  ('list_coms', '5'),
  ('list_imgs', '15'),
  ('list_microblog', '10'),
  ('list_rss', '10'),
  ('list_stats', '30'),
  ('loginnick', ''),
  ('loginpass', ''),
  ('mail', ''),
  ('menu_coms', '5'),
  ('menu_last_arts', '15'),
  ('menu_last_cats', '5'),
  ('menu_last_imgs', '6'),
  ('menu_microblog', '2'),
  ('menu_rand_arts', '5'),
  ('menu_rand_cats', '5'),
  ('menu_rand_imgs', '6'),
  ('menu_topvoted_arts', '5'),
  ('menu_top_arts', '5'),
  ('menu_top_coms_arts', '5'),
  ('microblog', 'Bleskovo o tom, čo všetko sa deje!'),
  ('microblog_head', 'Mikroblog'),
  ('needConfirm', '0'),
  ('order', 'id DESC'),
  ('pluginsAllowed', '1'),
  ('rewrite', '0'),
  ('sameoldarts', '1'),
  ('sep', '»'),
  ('similararts', '1'),
  ('staticlogin', ''),
  ('stats', '1'),
  ('template', 'default'),
  ('title', 'Moja stránka'),
  ('title_reverse', '0'),
  ('twittername', ''),
  ('twittertime', '2'),
  ('wysiwyg', 'texyla'),
  ('twitterbutton', '1'),
  ('startpage', 'menu'),
  ('admincolor', 'default'),
  ('googleplus', '1'),
  ('loginback', '');",
);

// Základná knižnica
if (@file_exists ('login/library.php')) {
 @include ('login/library.php');
 $step = isset ($_POST['step']) ? $_POST['step'] : 'home';
 switch ($step) {

  case 'friends':
   if (!@mysql_pconnect ($_SESSION['check_data']['server'], $_SESSION['check_data']['user'], $_SESSION['check_data']['password'])
   or !@mysql_select_db ($_SESSION['check_data']['database'])) break;
   if (!isset ($_POST['skip'])) {
    if (@mysql_query ("INSERT INTO `" . $_SESSION['check_data']['prefix'] . "_config` VALUES ('plugin_friends_ofhn', '" . adjust ($_POST['cofr']) . "');")
    and @mysql_query ("INSERT INTO `" . $_SESSION['check_data']['prefix'] . "_apps` VALUES (0, 'friends', 'Opiner Friends', 'Spravujte svoj profil, čítajte novinky a mnoho iného z portálu Opiner Friends, komunity okolo Opiner CMS.', '1.0.1', 'Ovalio', 'http://friends.tatarko.sk/', 5, 1, 1, 1, 0, 0, 0, 0, 1, 1, UNIX_TIMESTAMP(), 0);"))
    $out = '<p class="success">Prepojenie na Opiner Friends prebehlo úspešne!</p>' . "\n";
    else $out = '<p class="error">Nepodarilo sa uložiť informácie o prepojení na Opiner Friends!</p>' . "\n";
   } else $out = '';
   @unlink ('install.php');
   if (@file_exists ('install.php'))
   $out .= '<p>Inštalácia skončila, prosím odstráňte súbor install.php z Vašej domény!</p>' . "\n";
   else Header('Location: admin.php');
  break;

  case 'configuration':
   if (!@mysql_pconnect ($_SESSION['check_data']['server'], $_SESSION['check_data']['user'], $_SESSION['check_data']['password'])
   or !@mysql_select_db ($_SESSION['check_data']['database'])) break;
   mysql_query ("INSERT INTO `{$_SESSION['check_data']['prefix']}_moderators` VALUES(0, '" . adjust ($_POST['nick']) . "', '" . md5 ($_POST['password']) . "', NULL, '" . adjust ($_POST['nick']) . "', '" . adjust ($_POST['email']) . "', 1, 0, 1, 1, 1, NULL, 0)");
   $_SESSION['user'] = 1;
   $outto = @file_put_contents ('_config.php', '<?php
if (!defined ("in")) die();
$connect = array (
 "server" => ' . var_export ($_SESSION['check_data']['server'], true) . ',
 "user" => ' . var_export ($_SESSION['check_data']['user'], true) . ',
 "pass" => ' . var_export ($_SESSION['check_data']['password'], true) . ',
 "dbname" => ' . var_export ($_SESSION['check_data']['database'], true) . '
);
$prefix = ' . var_export ($_SESSION['check_data']['prefix'], true) . ';
?>') ? '' : '<p class="error">Nepodarilo sa však vytvoriť konfiguračný súbor, prosím otvorte _config.php na Vašej doméne a vložte doň nasledujúci kód:</p>
<code>&lt;?php<br />
if (!defined ("in")) die();<br />
$connect = array (<br />
&nbsp;&nbsp;"server" => ' . var_export ($_SESSION['check_data']['server'], true) . ',<br />
&nbsp;&nbsp;"user" => ' . var_export ($_SESSION['check_data']['user'], true) . ',<br />
&nbsp;&nbsp;"pass" => ' . var_export ($_SESSION['check_data']['password'], true) . ',<br />
&nbsp;&nbsp;"dbname" => ' . var_export ($_SESSION['check_data']['database'], true) . '<br />
);<br />
$prefix = ' . var_export ($_SESSION['check_data']['prefix'], true) . ';<br />
?&gt;</code>';
   $out = '<form action="install.php" method="post">
 <input type="hidden" name="step" value="friends" />
 <p class="success">Údaje úspešne uložené!</p>' . $outto . '
 <p>Odporúčame sa Vám registrovať na našom portáli <a href="http://friends.tatarko.sk/" target="_blank">Opiner Friends</a> a to jednoduchým kliknutím na Facebook tlačidlo Prihlásiť sa. Následne v nastaveniach svojho profilu nájdete Cofr kľúč na prepojenie Vašej novej stránky s Vaším účtom.</p>
 <strong>Cofr kľúč:</strong>
 <input type="text" name="cofr" />
 <input type="submit" name="skip" value="Preskočiť" />
 <input type="submit" value="Prepojiť" />
</form>';
  break;

  case 'check_connection':
   $out = '<form action="install.php" method="post">
 <input type="hidden" name="step" value="check_connection" />' . "\n";
   $_SESSION['check_data'] = array (
    'server'	=> $_POST['server'],
    'user'	=> $_POST['user'],
    'password'	=> $_POST['password'],
    'database'	=> $_POST['database'],
    'prefix'	=> $_POST['prefix'],
   );
   if (@mysql_pconnect ($_SESSION['check_data']['server'], $_SESSION['check_data']['user'], $_SESSION['check_data']['password'])
   and @mysql_select_db ($_SESSION['check_data']['database'])) {
    $ok = @mysql_query ("SET NAMES `utf8` COLLATE `utf8_general_ci`") ? true : false;
    foreach ($queries as $query)
    $ok = true and @mysql_query (str_replace ('%prefix%', $_SESSION['check_data']['prefix'], $query)) ? true : false;
    if ($ok) {
     $out = str_replace ('check_connection', 'configuration', $out);
     $out .= ' <p class="success">Databáza bola úspešne vytvorená!</p>
 <p>Teraz si nastavte svoje prihlasovacie údaje:</p>
 <strong>Prihlasovacie meno:</strong>
 <input type="text" name="nick" />
 <strong>Prihlasovacie heslo:</strong>
 <input type="password" name="password" />
 <strong>Váš email:</strong>
 <input type="text" name="email" />
 <input type="submit" value="Pokračovať" />' . "\n";
    } else $out .= ' <p class="error">Nepodarilo sa uložiť dáta do databázy!</p>
 <input type="submit" value="Opakovať" />' . "\n";
   } else $out .= ' <p class="error">Nepodarilo sa pripojiť k databáze!</p>
 <input type="submit" value="Opakovať" />' . "\n";
   $out .= '</form>';
  break;

  case 'database':
   $out = '<p>V tomto prvom kroku od Vás potrebujeme prihlasovacie údaje do MySQL databázy.</p>
<form action="install.php" method="post">
 <input type="hidden" name="step" value="check_connection" />
 <strong>Mysql server:</strong>
 <input type="text" name="server" />
 <strong>Užívateľ:</strong>
 <input type="text" name="user" />
 <strong>Prihlasovacie heslo:</strong>
 <input type="password" name="password" />
 <strong>Databáza:</strong>
 <input type="text" name="database" />
 <strong>Prefix:</strong>
 <input type="text" name="prefix" />
 <input type="submit" value="Overiť pripojenie" />
</form>';
  break;

  default:
   $out = '<form action="install.php" method="post">
 <input type="hidden" name="step" value="database" />
 <p>Vitajte pri inštalácií redakčného systému Opiner CMS, bude to trvať menej ako 5 minút a Vy budete mať svoju vlastnú stránku.</p>
 <input type="submit" value="Pokračovať" />
</form>';
  break;
 };
};
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $translate['info.short']; ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="copyright" content="Ovalio" />
	<meta name="author" content="Ovalio" />
	<meta name="robots" content="Noindex, Nofollow" />
	<link rel="shortcut icon" href="admin/images/favicon.png" type="image/png" />
	<link rel="stylesheet" href="login/style.css" type="text/css" media="all" />
	<title>Opiner CMS &raquo; Inštalácia</title>
</head>
<body>
<div id="container">
<div id="border">
<div id="content">
<?php echo $out; ?>
<div class="cleaner"></div>
<div id="footer"><p class="footer">©2012 <a href="http://opiner.tatarko.sk/" title="Oficiálne stránky projektu">Opiner CMS</a> vytvorilo <em>Ovalio</em></p></div>
</div></div></div>
</body>
</html>