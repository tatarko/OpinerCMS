<?php
if (!defined ('in')) exit ();
unset ($cache ['authors']);
$out .= '<p class="right">
 <a href="admin.php?what=redactors">' . TempIcon ('cat') . ' ' . $translate['redactors'] . '</a>
 <a href="admin.php?what=massmail">' . TempIcon ('cat') . ' ' . $translate['redactors.massmail'] . '</a>
 <a href="admin.php?what=confirm">' . TempIcon ('cat') . ' ' . $translate['redactors.confirm'] . '</a>
 <a href="admin.php?what=' . $what . '&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['redactors.add'] . '</a>
</p>'.n;




/*----- Tabuľka -----*/

if (!isset ($_GET['mod'])) {
	$out = HeadIfPost ($translate['redactors']);
	$sql = @mysql_query("SELECT `id`, `name`, `isadmin` FROM `{$prefix}_moderators` WHERE `id` != " . USER . " ORDER BY `isadmin` DESC, `name` ASC");
	$out .= '<table id="admintable" cellspacing="3px">
	<tr><th>' . $translate['account.name'] . '</th><th width="50px">' . $translate['action'] . '</th></tr>' . n;
	while ($tab = @mysql_fetch_assoc($sql)) {
		@++$iid;
		$out .= '<tr><td>' . (($tab['isadmin']) ? '<strong>' . $tab['name'] . '</strong>' : $tab['name']) . '</td><td><a href="?what=' . $what . '&mod=edit&id=' . $tab['id'] . '">'.TempIcon ('edit').'</a></td></tr>' . n;
	};
	if(!isset ($iid)) $out .= '<tr><td colspan="2">' . $translate['nocontent'] . '</td></tr>' . n;
	$out .= '</table>';





/*----- Pridávanie položky -----*/

} else if ($_GET['mod'] == 'add') {
	$out = HeadIfPost ($translate['redactors.add']);
	if (isset ($_GET['ok'])) {
		if (!@mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_moderators` WHERE `nick` = '".adjust($_POST['nick'])."' OR `mail` = '".adjust($_POST['mail'])."' LIMIT 1"))) {
			$isadmin = (isset ($_GET['isadmin'])) ? 1 : 0;
			$p1 = (isset ($_GET['p1'])) ? 1 : 0;
			$p2 = (isset ($_GET['p2'])) ? 1 : 0;
			$p3 = (isset ($_GET['p3'])) ? 1 : 0;
			$p4 = (isset ($_GET['p4'])) ? 1 : 0;
			if (@mysql_query ("INSERT INTO `{$prefix}_moderators` VALUES (0, '".adjust($_POST['nick'])."', '".md5($_POST['password'])."', '',  '".adjust($_POST['nick'])."', '".adjust($_POST['mail'])."', $isadmin, 0, $p1, 0, $p3, $p4, '" . adjust ($_POST['ahcm']) . "', $p2);"))
			Header ('Location: admin.php?what='. $what);
			else $out .= GetIcon ('error', $translate['failureadd']);
		} else $out .= GetIcon ('warning', $translate['redactors.reserved']);
	};
	$tohead[] = '<script src="admin/remote/js/redactors.js" type="text/javascript"></script>';
	$out .= '<form action="' . _SiteForm . '" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['redactors.nick'] . '</strong>
		<input type="text" name="nick" /><br />
		<em>' . $translate['redactors.nick.title'] . '</em>
		<strong>' . $translate['account.pass'] . '</strong>
		<input type="password" name="password" /><br />
		<em>' . $translate['redactors.pass.title'] . '</em>
		<strong>' . $translate['account.mail'] . '</strong>
		<input type="text" name="mail" /><br />
		<em>' . $translate['redactors.mail.title'] . '</em>
	</fieldset>
	<h2>' . $translate['redactors.rights'] . '</h2>
	<fieldset>
		<div style="float:left;width:50%;">
		<input type="checkbox" name="isadmin" id="isadmin" /> <label for="isadmin">' . $translate['redactors.isadmin'] . '</label><br />
		<span class="noadmin">
		<input type="checkbox" name="p1" id="p1" checked="checked" /> <label for="p1">' . $translate['redactors.right.arts'] . '</label><br />
		<input type="checkbox" name="p2" id="p2" checked="checked" /> <label for="p2">' . $translate['redactors.needConfirm'] . '</label><br />
		<input type="checkbox" name="p3" id="p3" checked="checked" /> <label for="p3">' . $translate['redactors.right.albums'] . '</label><br />
		<input type="checkbox" name="p4" id="p4" /> <label for="p4">' . $translate['redactors.right.apps'] . '</label><br />
		</span>
		</div>
		<div style="float:left;width:50%;" class="noadmin">
		<strong>' . $translate['redactors.ahcm'] . '</strong>
		<textarea name="ahcm" rows="2"></textarea><br />
		<em>' . $translate['redactors.ahcm.title'] . '</em>
		</div>
	</fieldset>
	<input type="submit" name="ok" value="' . $translate['add'] . '" />
	</form>';





/*----- Editacia -----*/

	} else if ($_GET['mod'] == 'edit' and $info = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `id` = " . adjust ($_GET['id'], true)))) {
	$out = HeadIfPost ($translate['redactors.edit']);
	if (isset ($_GET['ok'])) {
		$block = (isset ($_GET['blocked'])) ? 1 : 0;
		$isadmin = (isset ($_GET['isadmin'])) ? 1 : 0;
		$p1 = (isset ($_GET['p1'])) ? 1 : 0;
		$p2 = (isset ($_GET['p2'])) ? 1 : 0;
		$p3 = (isset ($_GET['p3'])) ? 1 : 0;
		$p4 = (isset ($_GET['p4'])) ? 1 : 0;
		$password = ($_GET['password'] != '') ? ", `password` = '".md5($_GET['password'])."'" : '';
		if (@mysql_query ("UPDATE `{$prefix}_moderators` SET `isadmin` = $isadmin, `articles` = $p1, `needConfirm` = $p2, `albums` = $p3, `plugins` = $p4, `name` = '".adjust($_GET['name'])."', `mail` = '".adjust($_GET['mail'])."', `blocked` = $block{$password}, `ahcm` = '" . adjust ($_POST['ahcm']) . "' WHERE `id` = ".adjust($_GET['id'],true)." LIMIT 1"))
		$out .= getIcon ('info', $translate['successact']);
		else $out .= getIcon ('error', $translate['failureact']);
		$info = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_moderators` WHERE `id` = " . adjust ($_GET['id'], true)));
	};
	$value = array ();
	$value[] = ($info['blocked'] == 1) ? ' checked="checked"' : '';
	$value[] = ($info['articles'] == 1) ? ' checked="checked"' : '';
	$value[] = ($info['needConfirm'] == 1) ? ' checked="checked"' : '';
	$value[] = ($info['albums'] == 1) ? ' checked="checked"' : '';
	$value[] = ($info['plugins'] == 1) ? ' checked="checked"' : '';
	$value[] = ($info['isadmin'] == 1) ? ' checked="checked"' : '';
	$tohead[] = '<script src="admin/remote/js/redactors.js" type="text/javascript"></script>';
	$out .= '<form action="' . _SiteForm . '" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['account.pass'] . '</strong>
		<input type="password" name="password" /><br />
		<em>' . $translate['account.pass.title'] . '</em>
		<strong>' . $translate['account.name'] . '</strong>
		<input type="text" name="name" value="' . setQ ($info['name']) . '" />
		<strong>' . $translate['account.mail'] . '</strong>
		<input type="text" name="mail" value="' . setQ ($info['mail']) . '" />
	</fieldset>
	<h2>' . $translate['redactors.rights'] . '</h2>
	<fieldset>
		<div style="float:left;width:50%;">
		<input type="checkbox" name="isadmin" id="isadmin"' . $value[5] . ' /> <label for="isadmin">' . $translate['redactors.isadmin'] . '</label><br />
		<span class="noadmin">
		<input type="checkbox" name="p1" id="p1"' . $value[1] . ' /> <label for="p1">' . $translate['redactors.right.arts'] . '</label><br />
		<input type="checkbox" name="p2" id="p2"' . $value[2] . ' /> <label for="p2">' . $translate['redactors.needConfirm'] . '</label><br />
		<input type="checkbox" name="p3" id="p3"' . $value[3] . ' /> <label for="p3">' . $translate['redactors.right.albums'] . '</label><br />
		<input type="checkbox" name="p4" id="p4"' . $value[4] . ' /> <label for="p4">' . $translate['redactors.right.apps'] . '</label><br />
		</span>
		<input type="checkbox" name="blocked" id="block"' . $value[0] . ' /> <label for="block">' . $translate['redactors.block'] . '</label>
		</div>
		<div style="float:left;width:50%;" class="noadmin">
		<strong>' . $translate['redactors.ahcm'] . '</strong>
		<textarea name="ahcm" rows="2">' . $info['ahcm'] . '</textarea><br />
		<em>' . $translate['redactors.ahcm.title'] . '</em>
		</div>
	</fieldset>
	<input type="submit" name="ok" value="' . $translate['save'] . '" />
	<input type="reset" value="' . $translate['reset'] . '" />
</form>';
} else Header ('Location: admin.php?what=' . $what);
?>