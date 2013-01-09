<?php
if (!defined ('in')) exit ();
$out = HeadIfPost ($translate['account.title']);
if (isset ($_GET['ok'])) {
	if ((!empty ($_POST['password']) and @mysql_query ("UPDATE `{$prefix}_moderators` SET `mail` = '" . adjust ($_POST['mail']) . "', `name` = '" . adjust ($_POST['name']) . "', `password` = '" . md5 ($_POST['password']) . "' WHERE `id` = " . USER))
	or @mysql_query ("UPDATE `{$prefix}_moderators` SET `mail` = '" . adjust ($_POST['mail']) . "', `name` = '" . adjust ($_POST['name']) . "' WHERE `id` = " . USER))
	$out .= GetIcon ('info', $translate['successact']);
	else $out .= GetIcon ('error', $translate['failureact']);
};
list ($mail, $name) = @mysql_fetch_row (@mysql_query ("SELECT `mail`, `name` FROM `{$prefix}_moderators` WHERE `id` = " . USER));
$out .= '<form action="' . _SiteForm . '" method="post">
<fieldset>
	<strong>' . $translate['account.pass'] . '</strong>
	<input type="password" name="password" /><br />
	<em>' . $translate['account.pass.title'] . '</em>
	<strong>' . $translate['account.name'] . '</strong>
	<input type="text" name="name" value="' . setQ ($name) . '" /><br />
	<em>' . $translate['account.name.title'] . '</em>
	<strong>' . $translate['account.mail'] . '</strong>
	<input type="text" name="mail" value="' . setQ ($mail) . '" /><br />
	<em>' . $translate['account.mail.title'] . '</em>
</fieldset>
<input type="submit" name="ok" value="' . $translate['save'] . '" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>';
?>