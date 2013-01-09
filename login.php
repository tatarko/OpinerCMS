<?php
session_start ();
error_reporting(E_ALL);
if (@file_exists ('install.php')) Header ('Location: install.php');

// Základná knižnica
if (@file_exists ('login/library.php') and @file_exists ('media/get-config.php')) {
	@include ('login/library.php');
	include_once ('media/get-config.php');
	$styles = empty ($_CONFIG['loginback']) ? '' : '<style type="text/css"> body { background: #555 url("' . $_CONFIG['loginback'] . '") no-repeat center top; } </style>' . "\n ";

	// Načítanie prekladu
	if (!isset ($_CONFIG['language'])) $_CONFIG['language'] = 'slovak';
	if (file_exists ("languages/{$_CONFIG['language']}.php")) {
		include ("languages/{$_CONFIG['language']}.php");
	} else exit (chyba ('nie je možné načítať preklad systému!'));
	
	// Veci okolo prihlasovania
	if (isset ($_POST['nickname'], $_POST['password'])) {
		if ($info = @mysql_fetch_assoc (@mysql_query ("SELECT `id` FROM `{$prefix}_moderators` WHERE `nick` = '" . adjust ($_POST['nickname']) . "' AND `password` = '" . md5 ($_POST['password']) . "' and `blocked` = 0 LIMIT 1"))) {
			$_SESSION['user'] = $info['id'];
			if (isset ($_POST['staticlogin'])) @mysql_query ("UPDATE `{$prefix}_moderators` SET `statichash` = '" . md5 (sha1 ($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'])) . "' WHERE `id` = {$info['id']} LIMIT 1");
			Header ('Location: admin.php');
		} else $out = '<p class="error">' . $translate['login.failed'] . '</p>';
	} else if (isset ($_GET['out'])) {
		if ($info = @mysql_fetch_assoc (@mysql_query ("SELECT `id` FROM `{$prefix}_moderators` WHERE `id` = " . adjust ($_SESSION['user'], true) . " LIMIT 1")))
		@mysql_query ("UPDATE `{$prefix}_moderators` SET `statichash` = '' WHERE `id` = {$info['id']} LIMIT 1");
		unset ($_SESSION['user']);
		$out = '<p class="success">' . $translate['logout.success'] . '</p>';
	} else if (isset ($_GET['lost'])) {
		$out = '<h3>' . $translate['login.lost.title'] . '</h3>
<form action="login.php" method="post">
	<input type="hidden" name="lost" value="password" />
	<strong>' . $translate['redactors.nick'] . ':</strong><br />
	<input type="text" name="nick" /><br />
	<strong>' . $translate['login.lost.new'] . ':</strong><br />
	<input type="password" name="pass1" /><br />
	<strong>' . $translate['login.lost.confirm'] . ':</strong><br />
	<input type="password" name="pass2" /><br />
	<input type="submit" value="' . $translate['login.lost.check'] . '" />
</form>
<div style="clear:both;"></div>';
	} else if (isset ($_POST['pass1'], $_POST['nick'], $_POST['pass2'])) {
		$nick = adjust ($_POST['nick']);
		if (!($mail = @mysql_fetch_row (@mysql_query ("SELECT `mail` FROM `{$prefix}_moderators` WHERE `nick` = '$nick' LIMIT 1")))) $out = '<p class="red">' . $translate['login.lost.er1'] . '</p>';
		else if ($_POST['pass1'] != $_POST['pass2'] or $_POST['pass1'] == '') $out = '<p class="red">' . $translate['login.lost.er2'] . '</p>';
		else if (!@mysql_query ("INSERT INTO `{$prefix}_iplog` VALUES (0, '$nick', 'changepass', '" . md5 ($_POST['pass1']) . "');")) $out = '<p class="red">' . $translate['login.lost.er3'] . '</p>';
		else {
			$linker = str_replace ('login.php', '', $_SERVER['PHP_SELF']);
			$linker = 'http://' . $_SERVER['SERVER_NAME'] . $linker;
			$hash = md5(mysql_insert_id());
			if (@mail ($mail[0], $translate['login.lost.subject'], langrep ('login.lost.text', $_SERVER['REMOTE_ADDR']) . ":\r\n\r\n{$translate['redactors.nick']}: $nick\r\n{$translate['login.lost.new']}: {$_POST['pass1']}\r\n\r\n{$linker}login.php?hash=$hash\r\n{$translate['login.lost.note']}"))
			$out = '<p class="green">' . $translate['login.lost.emailsucc'] . '</p>';
			else $out = '<p class="red">' . $translate['login.lost.emailfail'] . '</p>';
		};
	} else if (isset ($_GET['hash'])) {
		if ($info = @mysql_fetch_row (@mysql_query ("SELECT `ip`, `hodnota` FROM `{$prefix}_iplog` WHERE `what` = 'changepass' AND MD5(`id`) = '" . adjust ($_GET['hash']) . "' LIMIT 1")))
		$out = '<p class="green">' . $translate['login.lost.success'] . '</p>';
		else $out = '<p class="red">' . $translate['login.lost.failure'] . '</p>';
	} else {
		$out = '	<strong>' . $translate['redactors.nick'] . '</strong><br />
	<input type="text" name="nickname" /><br />
	<strong>' . $translate['account.pass'] . '</strong><br />
	<input type="password" name="password" /><br />
	<input type="checkbox" name="staticlogin" id="staticlogin" /> <label for="staticlogin">' . $translate['login.static'] . '</label><br />
	<a id="lostpass" href="login.php?lost">' . $translate['login.lost.title'] . '</a>
	<input type="submit" value="' . $translate['login.submit'] . '" />';	
	};
} else $out = '<p class="error">' . $translate['login.error'] . '</p>';
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
 <?php echo $styles; ?><title>Opiner CMS &raquo; <?php echo $translate['login.title'];?></title>
</head>
<body>
<div id="container">
<div id="border">
<div id="content">
<form method="post" action="login.php">
<?php echo $out; ?>
</form>
<div class="cleaner"></div>
<div id="footer"><p class="footer"><a href="./" title="<?php echo $translate['gosite']; ?>"><?php echo $_CONFIG['title'];?></a><br />2011 © Ovalio</p></div>
</div></div></div>
</body>
</html>