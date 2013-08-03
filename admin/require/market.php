<?php
$out = HeadIfPost ($translate['apps.market']);





/*--- Nastavenia aplikácie ---*/
if (isset ($_REQUEST['settings'])
and $info = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_apps` WHERE `id` = " . adjust ($_REQUEST['settings'], true) . " LIMIT 1"))
and $app = OpinerAutoLoader::loadPlugin ($info['fname'])) {
if (isset ($_POST['save'])) {
	$value = array ();
	$value[] = (isset ($_POST['application'])) ? 1 : 0; #0
	$value[] = (isset ($_POST['widget'])) ? 1 : 0;
	$value[] = (isset ($_POST['plugin'])) ? 1 : 0;
	$value[] = (isset ($_POST['hcm'])) ? 1 : 0; #3
	$value[] = (isset ($_POST['redactors'])) ? 1 : 0;
	$value[] = (isset ($_POST['homepage'])) ? 1 : 0;
	$value[] = (isset ($_POST['static'])) ? 1 : 0; #6
	if (@mysql_query ("UPDATE `{$prefix}_apps` SET `title` = '" . adjust ($app -> title) . "', `description` = '" . adjust ($app -> description) . "', `version` = '" . adjust ($app -> version) . "', `author` = '" . adjust ($app -> author) . "', `url` = '" . adjust ($app -> url) . "', `allowed` = " . adjust ($_POST['allowed'], true) . ", `application` = $value[0], `widget` = $value[1], `plugin` = $value[2], `hcm` = $value[3], `static` = $value[6], `redactors` = $value[4], `homepage` = $value[5], `position` = " . adjust ($_POST['position'], true) . " WHERE `id` = " . adjust ($_GET['settings']) . " LIMIT 1")
	and (($app -> hasSettings () and $app -> saveOptions ($_REQUEST['setting'])) or true))
	$out .= getIcon ('info', $translate['successact']);
	else $out .= getIcon ('error', $translate['failureact']);
	$info = @mysql_fetch_assoc (@mysql_query ("SELECT * FROM `{$prefix}_apps` WHERE `id` = " . adjust ($_REQUEST['settings'], true) . " LIMIT 1"));
	unset ($cache['widget_' . $info['fname']]);
};
$out .= '<form action="' . _SiteForm . '" method="post">
<h2>' . $translate['apps.sysset'] . '</h2>
<fieldset>
<strong>' . $translate['apps.status'] . '</strong>
<input type="radio" name="allowed" value="1"' . (($info['allowed']==1)?' checked="checked"':'') . ' id="allowed1" /> <label for="allowed1">' . $translate['apps.allowed'] . '</label><br />
<input type="radio" name="allowed" value="0"' . (($info['allowed']==0)?' checked="checked"':'') . ' id="allowed2" /> <label for="allowed2">' . $translate['apps.blocked'] . '</label>
<strong>' . $translate['apps.almods'] . '</strong>
<input type="checkbox" name="application"' . ((!$app -> canRun ('application'))?' readonly="readonly" disabled="disabled"':'') . (($info['application']==1 and $app->canRun('application'))?' checked="checked"':'') . ' id="application" /> <label for="application">' . $translate['apps.app'] . '</label><br />
<input type="checkbox" name="widget"' . ((!$app -> canRun ('widget'))?' readonly="readonly" disabled="disabled"':'') . (($info['widget']==1 and $app->canRun('widget'))?' checked="checked"':'') . ' id="widget" /> <label for="widget">' . $translate['apps.widget'] . '</label><br />
<input type="checkbox" name="plugin"' . ((!$app -> canRun ('plugin'))?' readonly="readonly" disabled="disabled"':'') . (($info['plugin']==1 and $app->canRun('plugin'))?' checked="checked"':'') . ' id="plugin" /> <label for="plugin">' . $translate['apps.plugin'] . '</label><br />
<input type="checkbox" name="hcm"' . ((!$app -> canRun ('hcm'))?' readonly="readonly" disabled="disabled"':'') . (($info['hcm']==1 and $app->canRun('hcm'))?' checked="checked"':'') . ' id="hcm" /> <label for="hcm">' . $translate['apps.hcm'] . '</label><br />
<input type="checkbox" name="static"' . ((!$app -> canRun ('staticrun'))?' readonly="readonly" disabled="disabled"':'') . (($info['static']==1 and $app->canRun('staticrun'))?' checked="checked"':'') . ' id="static" /> <label for="static">' . $translate['apps.static'] . '</label>
<strong>' . $translate['othersettings'] . '</strong>
<input type="checkbox" name="redactors"' . (($app -> onlyAdmin ())?' readonly="readonly" disabled="disabled"':'') . (($info['redactors']==1 and !$app->onlyAdmin())?' checked="checked"':'') . ' id="redactors" /> <label for="redactors">' . $translate['apps.redactors'] . '</label><br />
<input type="checkbox" name="homepage"' . ((!$app -> canRun ('application'))?' readonly="readonly" disabled="disabled"':'') . (($info['homepage']==1 and $app->canRun('application'))?' checked="checked"':'') . ' id="homepage" /> <label for="homepage">' . $translate['apps.homepage'] . '</label><br />
<input type="text" name="position" value="' . $info['position'] . '" class="smallint" /> <em>' . $translate['apps.wpos'] . '</em>
</fieldset>'.n;
if ($app -> hasSettings ()) $out .= '<h2>' . $translate['apps.appset'] . '</h2>
<fieldset>
' . $app -> options () . '
</fieldset>'.n;
$out .= '<input type="submit" value="' . $translate['save'] . '" name="save" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>'.n;





/*--- Inštalácia aplikácie ---*/

} else if (isset ($_REQUEST['install'])) {
if (!@mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_apps` WHERE `fname` = '" . adjust ($_REQUEST['install']) . "' LIMIT 1"))) {
	if ($app = OpinerAutoLoader::loadPlugin ($_REQUEST['install'])
	and @mysql_query ("INSERT INTO `{$prefix}_apps` VALUES (0, '" . adjust ($_REQUEST['install']) . "', '" . adjust ($app -> title) . "', '" . adjust ($app -> description) . "', '" . adjust ($app -> version) . "', '" . adjust ($app -> author) . "', '" . adjust ($app -> url) . "', 0, 1, 1, " . (($app -> canRun ('application'))?1:0) . ", " . (($app -> canRun ('widget'))?1:0) . ", " . (($app -> canRun ('hcm'))?1:0) . ", " . (($app -> canRun ('plugin'))?1:0) . ", " . (($app -> canRun ('staticrun'))?1:0) . ", " . (($app -> onlyAdmin())?0:1) . ", " . $app -> hasCache () . ", 1, UNIX_TIMESTAMP(), 0);")
	and $app -> runDB())
	$out .= getIcon ('info', $translate['successact']);
	else $out .= getIcon ('error', $translate['failureact']);
} else $out .= getIcon ('warning', $translate['apps.alins']);





/*--- Vzdialená inštalácia aplikácie ---*/

} else if (isset ($_REQUEST['cofr'], $_REQUEST['fname'])) {
if (!@mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_apps` WHERE `fname` = '" . adjust ($_REQUEST['install']) . "' LIMIT 1"))) {
	if (@file_put_contents ('admin/apps/' . $_REQUEST['fname'] . '.php', file_get_contents ($_REQUEST['cofr']))) {
		$app = OpinerAutoLoader::loadPlugin ($_REQUEST['fname']);
		if (@mysql_query ("INSERT INTO `{$prefix}_apps` VALUES (0, '" . adjust ($_REQUEST['fname']) . "', '" . adjust ($app -> title) . "', '" . adjust ($app -> description) . "', '" . adjust ($app -> version) . "', '" . adjust ($app -> author) . "', '" . adjust ($app -> url) . "', 0, 1, 1, " . (($app -> canRun ('application'))?1:0) . ", " . (($app -> canRun ('widget'))?1:0) . ", " . (($app -> canRun ('plugin'))?1:0) . ", " . (($app -> canRun ('hcm'))?1:0) . ", " . (($app -> canRun ('staticrun'))?1:0) . ", " . (($app -> onlyAdmin())?0:1) . ", " . $app -> hasCache () . ", 1, UNIX_TIMESTAMP(), 0);"))
		$out .= getIcon ('info', $translate['successact']);
		else $out .= getIcon ('error', $translate['failureact']);
	} else $out .= getIcon ('warning', $translate['apps.cannotdown']);
} else $out .= getIcon ('warning', $translate['apps.alins']);





/*--- Vzdialená aktualizovanie aplikácie ---*/

} else if (isset ($_REQUEST['url'], $_REQUEST['update'])) {
if ($info = @mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_apps` WHERE `fname` = '" . adjust ($_REQUEST['update']) . "' LIMIT 1"))) {
	if (@file_put_contents ('admin/apps/' . $_REQUEST['update'] . '.php', file_get_contents ($_REQUEST['url']))) {
		$app = OpinerAutoLoader::loadPlugin ($_REQUEST['update']);
		if (@mysql_query ("UPDATE `{$prefix}_apps` SET `version` = '" . adjust ($app -> version) . "' WHERE `id` = $info[0] LIMIT 1"))
		$out .= getIcon ('info', $translate['successact']);
		else $out .= getIcon ('error', $translate['failureact']);
	} else $out .= getIcon ('warning', $translate['apps.cannotdown']);
};





/*--- Odinštalácia aplikácie ---*/

} else if (isset ($_REQUEST['uninstall'])) {
if ($info = @mysql_fetch_assoc (@mysql_query ("SELECT `fname` FROM `{$prefix}_apps` WHERE `id` = " . adjust ($_REQUEST['uninstall'], true) . " LIMIT 1"))) {
	if ($app = OpinerAutoLoader::loadPlugin ($info['fname'])
	and $app -> uninstall ()
	and @mysql_query ("DELETE FROM `{$prefix}_apps` WHERE `id` = " . adjust ($_REQUEST['uninstall'], trur) . " LIMIT 1"))
	$out .= getIcon ('info', $translate['successact']);
	else $out .= getIcon ('error', $translate['failureact']);
} else Header ('Location: ?what=market');





/*--- Odstránenie aplikácie ---*/

} else if (isset ($_REQUEST['drop'])) {
if (@unlink ('admin/apps/' . $_REQUEST['drop'] . '.php')
and !@file_exists ('admin/apps/' . $_REQUEST['drop'] . '.php'))
Header ('Location: ?what=market');
else $out .= getIcon ('error', $translate['failureact']);




/*--- Domovská stránka ---*/

}; if (!isset ($_POST['settings'])) {
$out .= '<h2>' . $translate['apps.manage'] . '</h2>
<table id="admintable" cellspacing="3px">
	<tr><th>' . $translate['title'] . '</th><th>' . $translate['apps.author'] . '</th><th>' . $translate['apps.status'] . '</th><th>' . $translate['action'] . '</th></tr>
	<tr><td colspan="4"><span class="nameyes">' . $translate['apps.appinstalled'] . '</span></td></tr>'.n;
$sql = @mysql_query ("SELECT `id`, `title`, `version`, `author`, `url`, `allowed`, `application`, `fname`, SHA1(CONCAT(`id`, `fname`, `installed`)) as `hash` FROM `{$prefix}_apps` ORDER BY `title` ASC");
while ($info = @mysql_fetch_assoc ($sql)) {
	$out .= '	<tr>
		<td>' . $info['title'] . ' (' . $info['version'] . ')</td>
		<td><a href="' . $info['url'] . '" target="_blank">' . $info['author'] . '</a></td>
		<td>' . (($info['allowed']==1) ? $translate['apps.installed'] : $translate['apps.blocked']) . '</td>
		<td><a href="?what=market&settings=' . $info['id'] . '">' . $translate['settings'] . '</a> | <a href="?what=market&uninstall=' . $info['id'] . '">' . $translate['apps.uninstall'] . '</a>' . (($info['allowed']==1 and $info['application']==1)?' | <a href="admin.php?app=' . $info['hash'] . '">' . $translate['apps.run'] . '</a>':'') . '</td>
	</tr>'.n;
	$apps[] = $info['fname'];
};
$out .= '	<tr><td colspan="4"><span class="nameno">' . $translate['apps.appnotinstalled'] . '</span></td></tr>'.n;
$dir = opendir ('admin/apps');
while ($file = readdir ($dir)) {
	$array_apps = isset ($apps) ? $apps : array ();
	if ($name = substr ($file, 0, strrpos ($file, '.php')) and $name != '' and array_search ($name, $array_apps) === false and false !== ($app = OpinerAutoLoader::loadPlugin ($name))) {
		$out .= '	<tr>
		<td>' . $app -> title . ' (' . $app -> version . ')</td>
		<td><a href="' . $app -> url . '" target="_blank">' . $app -> author . '</a></td>
		<td>' . $translate['apps.prepared'] . '</td>
		<td><a href="?what=market&install=' . $name . '">' . $translate['apps.install'] . '</a> | <a href="?what=market&drop=' . $name . '">' . $translate['apps.drop'] . '</a></td>
	</tr>'.n;
	};
};
$out .= '</table>
<h2>' . $translate['apps.market'] . '</h2>'.n;
if (false !== ($xml = simplexml_load_file ('http://friends.tatarko.sk/cofr.php?market' . ((isset ($apps)) ? '=' . implode ('~', $apps) : '')))
and isset ($xml['protocol'], $xml -> application) and $xml['protocol'] == '1.0') {
	$tohead[] = '<style type="text/css">
	#market { width: 100%; }
	#market td { padding: 8px 12px; vertical-align: top;  width: 25%; -moz-border-radius: 5px; -webkit-border-radius: 5px; background: #EDF2F9; }
	#market td h3 { margin: 0; padding: 0; }
	#market td p { margin: 0; padding: 0; }
	#market td img { width: 48px; height: 48px; float: left; margin: 2px 8px 0 0; }
	#market .gravatar { width: 16px; height: 16px; float: none; margin: -4px 0; }		
	#market .meta { padding: 10px 0 5px; margin: 0; clear:left; text-align: center; font-size: 9px; }
	#market .buttons a { margin: 0 0 0 5px; background: #7397c3 url("admin/remote/img/bg.png") repeat-x center center; border: 1px solid #396CBC; color: #fff; padding: 3px 8px; font: bold 9px Tahoma, serif; cursor: pointer; border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius: 2px; }
</style>';
	$out .= '<table id="market"><tr>'.n;
	foreach ($xml -> application as $app) {
		$out .= '	<td>
		<h3><a href="' . $app -> profile . '" target="_blank">' . $app -> title . ' ' . $app -> version . '</a></h3>
		<img src="' . $app -> image . '" />
		<p>' . $app -> description . '</p>
		<p class="meta">
			<img src="' . $app -> author -> image . '" class="gravatar" />
			<a href="' . $app -> author -> url . '" target="_blank">' . $app -> author -> name . '</a>, '
			 . langrep ('apps.installs', $app -> installed) . '
			<span class="buttons">
				<a href="' . $app -> file . '">' . $translate['apps.download'] . '</a>
				<a href="?what=market&cofr=' . $app -> file . '&amp;fname=' . $app -> fname . '">' . $translate['apps.install'] . '
			</span>
		</p>
	</td>'.n;
		if (@++$i % 3 == 0) $out .= '</tr><tr>'.n;
	};
	$out .= '</tr></table>';
} else $out .= '<p>' . $translate['apps.emptymarket'] . '</p>';
};
?>