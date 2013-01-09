<?php
if(!defined("in"))exit();
$out = HeadIfPost ($translate['redactors.massmail']);
$out .= '<p class="right">
 <a href="admin.php?what=redactors">' . TempIcon ('cat') . ' ' . $translate['redactors'] . '</a>
 <a href="admin.php?what=massmail">' . TempIcon ('cat') . ' ' . $translate['redactors.massmail'] . '</a>
 <a href="admin.php?what=confirm">' . TempIcon ('cat') . ' ' . $translate['redactors.confirm'] . '</a>
</p>'.n;


if (isset ($_GET['subject'], $_GET['email'])) {
	$sql = @mysql_query ("SELECT `name`, `mail` FROM `{$prefix}_moderators`");
	while ($r = @mysql_fetch_row ($sql)) $mail[] = $r[0] . ' <' . $r[1] . '>';
	if (@mail (implode (', ', $mail), $_GET['subject'], $_GET['email'], "MIME-Version: 1.0\r\nContent-Type: text/html; charset=\"UTF-8\"\r\nFrom: {$_CONFIG['author']} <{$_CONFIG['mail']}>\r\nReply-To: {$_CONFIG['author']} <{$_CONFIG['mail']}>\r\nReturn-Path: {$_CONFIG['author']} <{$_CONFIG['mail']}>\r\nX-Mailer: Opiner CMS\r\n"))
	$out .= geticon ('info', $translate['successact']);
	else $out .= geticon ('error', $translate['failureact']);
};


$out .= '<form action="' . _SiteForm . '" method="post">
<fieldset>
	<strong>' . $translate['redactors.subject'] . '</strong>
	<input type="text" name="subject" />
	<strong>' . $translate['redactors.email'] . '</strong>
	<textarea name="email" rows="10"></textarea>
</fieldset>
<input type="submit" value="' . $translate['redactors.send'] . '" />
</form>'.n;
?>