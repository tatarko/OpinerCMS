<?php

if (!defined ('in')) exit ();

$allTables = array ('apps', 'cats', 'clanky', 'config', 'download', 'gall', 'img', 'links', 'menu', 'microblog', 'moderators', 'polls', 'sec', 'comments', 'iplog', 'visits', 'hits');
function delTree($dir) {
	$files = glob ($dir . '*', GLOB_MARK);
	foreach ($files as $file) {
		if (is_dir ($file)) delTree ($file);
		else unlink ($file);
	};
	if (is_dir ($dir)) rmdir ($dir);
	return true;
};

 
$out = HeadIfPost ($translate['dbman']);
if (!isset ($_GET['mod'])) $_GET['mod'] = 'home';
switch ($_GET['mod']) {

case 'import':
$out .= '<h2>' . $translate['dbman.import'] . '</h2>
<p>' . langrep ('dbman.import.notice', '<strong>/store/cache</strong>', '<strong>/</strong>', '<strong>import.php</strong>') . "</p>\n";
if (@file_exists ('import.php')) {
	if (isset ($_GET['import'])) {
		include ('import.php');
		if ($opiner == in) {
			$isOK = true;
			foreach ($allTables as $table) {
				@mysql_query ("TRUNCATE `{$prefix}_$table`;");
				if (isset ($data[$table])) {
					foreach ($data[$table] as $values) {
						foreach ($values as $index => $value) $values[$index] = adjust ($value);
						$isOK = ($isOK and @mysql_query ("INSERT INTO `{$prefix}_$table` VALUES ('" . implode ("', '", $values) . "');")) ? true : false;
					};
				};
			};
			$_SESSION['user'] = $data['config'][4][1];
			if ($isOK) $out .= getIcon ('info', $translate['dbman.import.succ']);
			else $out .= getIcon ('error', $translate['dbman.import.fail']);
			@unlink ('import.php');
		} else $out .= getIcon ('error', $translate['dbman.import.error']);
	} else {
		$out .= getIcon ('warning', $translate['dbman.import.warning']);
		$out .= '<form action="' . _SiteForm . '" method="post">
<input type="submit" name="import" value="' . $translate['continue'] . '" />
</form>'.n;
	};
};break;



case 'export':
$out .= '<h2>' . $translate['dbman.export'] . '</h2>
<form action="' . _SiteForm . '" method="post">
<p>' . $translate['dbman.export.notice'] . '</p>
<dl>
	<dt>' . $translate['dbman.export.type1'] . '</dt>
	<dd><input type="radio" name="type" value="content" id="t1" checked="checkek" /> <label for="t1">' . $translate['dbman.export.type1'] . '</label></dd>
	<dt>' . $translate['dbman.export.type2'] . '</dt>
	<dd><input type="radio" name="type" value="comments" id="t2" /> <label for="t2">' . $translate['dbman.export.type2t'] . '</label></dd>
	<dt>' . $translate['dbman.export.type3'] . '</dt>
	<dd><input type="radio" name="type" value="iplog" id="t3" /> <label for="t3">' . $translate['dbman.export.type3t'] . '</label></dd>
	<dt>' . $translate['dbman.export.type4'] . '</dt>
	<dd><input type="radio" name="type" value="complete" id="t4" /> <label for="t4">' . $translate['dbman.export.type4t'] . '</label></dd>
</dl>
<input type="submit" value="' . $translate['dbman.export.submit'] . '" />
</form>'.n;

if (isset ($_GET['type']) and !empty ($_GET['type'])) {
	switch ($_GET['type']) {
		case 'complete':
			$q = @mysql_query ("SHOW TABLES LIKE '{$prefix}%'");
			while ($t = mysql_fetch_row ($q)) $tables[] = str_replace ("{$prefix}_", '', $t[0]);
		break;
		case 'comments': $tables = array ('cats', 'clanky', 'config', 'download', 'gall', 'img', 'links', 'menu', 'microblog', 'moderators', 'polls', 'sec'); break;
		case 'iplog': $tables = array ('cats', 'clanky', 'config', 'download', 'gall', 'img', 'links', 'menu', 'microblog', 'moderators', 'polls', 'sec', 'iplog', 'comments'); break;
		default: $tables = array ('cats', 'clanky', 'config', 'download', 'gall', 'img', 'links', 'menu', 'microblog', 'moderators', 'polls', 'sec', 'comments'); break;
	};
	$data = array ();
	foreach ($tables as $table) {
		$data[$table] = array ();
		$sql = @mysql_query ("SELECT * FROM `{$prefix}_$table`");
		while ($values = @mysql_fetch_row ($sql)) $data[$table][] = $values;
	};
	$flname = 'store/cache/backup_' . date ('YmdHis') . '.php';
	if (@file_put_contents ($flname, "<?php\n\$type = '{$_GET['type']}';\n\$opiner = '" . in . "';\n\$data = " . var_export ($data, true) . ";\n?>"))
	$out .= getIcon ('info', langrep ('dbman.export.succ', $flname));
	else $out .= getIcon ('error', $translate['dbman.export.fail']);
};
break;



case 'reinstall':
$out .= '<h2>' . $translate['dbman.reinstall'] . '</h2>'.n;
$out .= getIcon ('warning', $translate['dbman.reinstall.warn']);
$out .= '<form action="' . _SiteForm . '" method="post">
<input type="checkbox" name="dropstore" checked="checked" id="l1" />
<label for="l1">' . $translate['dbman.reinstall.store'] . '</label><br />
<input type="checkbox" name="droptemps" checked="checked" id="l2" />
<label for="l2">' . $translate['dbman.reinstall.temps'] . '</label><br />
<input type="submit" value="' . $translate['dbman.reinstall.submit'] . '" name="reinstall" />
</form>'.n;

if (isset ($_GET['reinstall'])) {
	foreach ($allTables as $table)
	@mysql_query ("TRUNCATE `{$prefix}_$table`;");
	@mysql_query ("INSERT INTO `{$prefix}_config` (`nazov`, `hodnota`) VALUES 
		('list_admin', '10'),
		('rewrite', '0'),
		('autologin', '0'),
		('loginnick', 'admin'),
		('loginpass', MD5('admin')),
		('title', 'Lores Ipsum'),
		('desc', 'Lorem ipsum dolor sit amet consectetuer ac pellentesque consequat montes Lorem'),
		('keys', 'lorem, ipsum, dolor, sit, amet, consectetuer, ac, pellentesque, consequat, montes'),
		('author', 'Tvoje meno'),
		('mail', 'email@domena.sk'),
		('sep', '»'),
		('foot', 'Text, ktorý bude v pätičke'),
		('stats', '1'),
		('order', 'id DESC'),
		('template', 'Carmen'),
		('imgbrowser', 'shadowbox'),
		('wysiwyg', 'texyla'),
		('homepage', '1'),
		('list_arts', '10'),
		('list_cats', '10'),
		('list_imgs', '15'),
		('list_coms', '5'),
		('list_rss', '10'),
		('list_stats', '30'),
		('list_microblog', '10'),
		('menu_last_arts', '5'),
		('menu_rand_arts', '5'),
		('menu_top_arts', '5'),
		('menu_last_cats', '5'),
		('menu_rand_cats', '5'),
		('menu_last_imgs', '6'),
		('menu_rand_imgs', '6'),
		('menu_coms', '5'),
		('menu_microblog', '2'),
		('microblog_head', 'Novinky'),
		('microblog', 'Vaše bleskové novinky'),
		('global_comments', '1'),
		('global_reads', '1'),
		('global_author', '1'),
		('admin_foot_link', '1'),
		('favicon', 'favicon.png'),
		('favicon_mime', 'image/png'),
		('menu_topvoted_arts', '5'),
		('title_reverse', '1'),
		('global_voting', '1'),
		('global_linkers', '1'),
		('pluginsAllowed', '1'),
		('ahp_plugins', '1'),
		('pluginsInMenu', 'friends#feedback'),
		('blockIP', ''),
		('friends_mese', '1'),
		('friends_ofhn', ''),
		('conntype', '1')
		('needConfirm', '1'),
		('menu_top_coms_arts', '5'),
		('staticlogin', ''),
		('twittername', ''),
		('twittertime', '30'),
		('antispam', '1'),
		('language', 'slovak'),
		('facebooklike', '1'),
		('similararts', '1'),
		('sameoldarts', '1'),
		('twitterbutton', '1');");
	@mysql_query ("INSERT INTO `{$prefix}_menu` VALUES (0, 1, 0, 1, 'Hlavné menu', '<%mainmenu%>', '');");
	@mysql_query ("INSERT INTO `{$prefix}_sec` VALUES (0, 'Úvod', 'uvod', '<h1 align=\"center\">Opiner CMS</h1>\n<p>Gratulujeme Vám, úspešne ste nainštalovali redakčný systém Opiner CMS a teraz sa už nachádzate na Vašej stránke. Ak nechcete, aby tu bol tento úvodný text, tak sa pomocou prihlasovacích údajov <b>admin</b> (Meno), <b>admin</b> (Heslo) prihláste do administrácie (<a href=\"login.php\">prejsť na prihlasovanie</a>), kde celkovo spravujete Vašu stránku.</p>', 0, 0, 1);");
	$_SESSION['user'] = md5 ('admin');
	$out .= getIcon ('info', $translate['dbman.reinstall.succ']);
	unset ($array);
	if (isset ($_GET['droptemps'])) {
		$dir = opendir ('templates');
		while ($file = readdir ($dir)) {
			if (array_search ($file, array ('.', '..', 'Carmen', 'default')) === false)
			$array[] = "/templates/$file: ".((delTree("templates/$file"))?'OK':'ER');
		};
	};
	if (isset ($_GET['dropstore'])) {
		$dirs = array ('cache', 'files', 'gallery', 'gsmall', 'gravatars', 'icons');
		foreach ($dirs as $diro) {
			$dir = opendir ("store/$diro");
			while ($file = readdir ($dir)) {
				if (array_search ($file, array ('.', '..')) === false)
				$array[] = "/store/$diro/$file: ".((@unlink("store/$diro/$file")) ? 'OK' : 'ER');
			};
		};
	};
	if (isset ($array)) $out .= '<p>' . implode ("<br />\n", $array) . '</p>';
};
break;



case 'uninstall':
$out .= '<h2>' . $translate['dbman.uninstall'] . '</h2>
<p>' . $translate['dbman.uninstall.note'] . '</p>' . getIcon ('warning', $translate['dbman.uninstall.warn']) . '
<form action="' . _SiteForm . '" method="post">
<input type="submit" name="submit" value="' . $translate['dbman.uninstall.submit'] . '" />
</form>';
if (isset ($_POST['submit'])) {
	$success = true;
	foreach ($allTables as $table)
	$success = $success and @mysql_query ("DROP TABLE `{$prefix}_$table`;") ? true : false;
	$out .= $success ? getIcon ('info', $translate['dbman.uninstall.succ']) : getIcon ('error', $translate['dbman.uninstall.fail']);
};
break;



default:
$out .= '<dl>
	<dt><a href="admin.php?what=' . $what . '&amp;mod=import">' . $translate['dbman.import'] . '</a></dt>
	<dd>' . $translate['dbman.import.title'] . '</dd>
	<dt><a href="admin.php?what=' . $what . '&amp;mod=export">' . $translate['dbman.export'] . '</a></dt>
	<dd>' . $translate['dbman.export.title'] . '</dd>
	<dt><a href="admin.php?what=' . $what . '&amp;mod=uninstall">' . $translate['dbman.uninstall'] . '</a></dt>
	<dd>' . $translate['dbman.uninstall.title'] . '</dd>
	<dt><a href="admin.php?what=' . $what . '&amp;mod=reinstall">' . $translate['dbman.reinstall'] . '</a></dt>
	<dd>' . $translate['dbman.reinstall.title'] . '</dd>
</dl>';
break;
};
?>