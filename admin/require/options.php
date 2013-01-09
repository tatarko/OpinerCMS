<?php
if (!defined ('in')) exit ();
if (!isset ($_GET['mod'])) $_GET['mod'] = 'site-info';
$out .= '<p class="right">
 <a href="admin.php?what=' . $what . '&amp;mod=site-info">' . TempIcon ('cat') . ' ' . $translate['settings.site'] . '</a>
 <a href="admin.php?what=' . $what . '&amp;mod=functions">' . TempIcon ('cat') . ' ' . $translate['settings.functions'] . '</a>
 <a href="admin.php?what=' . $what . '&amp;mod=limits">' . TempIcon ('cat') . ' ' . $translate['settings.limits'] . '</a>
 <a href="admin.php?what=' . $what . '&amp;mod=admin">' . TempIcon ('cat') . ' ' . $translate['controlpanel'] . '</a>
 <a href="admin.php?what=microblog">' . TempIcon ('cat') . ' ' . $_CONFIG['microblog_head'] . '</a>
 <a href="admin.php?what=' . $what . '&amp;mod=connect">' . TempIcon ('cat') . ' ' . $translate['settings.database'] . '</a>
</p>'.n;







/*-------- POSTY -------*/	if (isset ($_GET['ok'])) {
switch ($_GET['mod']) {





/*----- SITE-INFO -----*/



case 'site-info':
if (ConfigUpdate ('title', $_GET['title']) and ConfigUpdate ('desc', $_GET['desc']) and ConfigUpdate ('keys', $_GET['keys'])
and ConfigUpdate ('sep', $_GET['sep']) and ConfigUpdate ('foot', $_GET['footinfo']) and ConfigUpdate ('title_reverse', $_GET['trev'])
and ConfigUpdate ('blockIP', $_GET['block'])) $out .= GetIcon ('info', $translate['successact']);
else $out .= GetIcon ('error', $translate['failureact']);
break;





/*----- FUNCTIONS -----*/



case 'functions':
$InputValue1 = (isset ($_GET['stats'])) ? 1 : 0;
$InputValue2 = (isset ($_GET['global_comments'])) ? 1 : 0;
$InputValue3 = (isset ($_GET['global_reads'])) ? 1 : 0;
$InputValue4 = (isset ($_GET['global_author'])) ? 1 : 0;
$InputValue5 = (isset ($_GET['rewrite'])) ? 1 : 0;
$InputValue6 = (isset ($_GET['foot_link'])) ? 1 : 0;
$InputValue8 = (isset ($_GET['global_voting'])) ? 1 : 0;
$InputValue9 = (isset ($_GET['global_linkers'])) ? 1 : 0;
$InputValue0 = (isset ($_GET['antispam'])) ? 1 : 0;
$InputValueA = (isset ($_GET['fblike'])) ? 1 : 0;
$InputValueB = (isset ($_GET['similararts'])) ? 1 : 0;
$InputValueC = (isset ($_GET['sameoldarts'])) ? 1 : 0;
$InputValueD = (isset ($_GET['needConfirm'])) ? 1 : 0;
$InputValueE = (isset ($_GET['ovasemedia'])) ? 1 : 0;
$InputValueF = (isset ($_GET['twitter'])) ? 1 : 0;
$InputValueG = (isset ($_GET['googleplus'])) ? 1 : 0;
if (ConfigUpdate ('stats', $InputValue1) and ConfigUpdate ('admin_foot_link', $InputValue6) and ConfigUpdate ('template', $_GET['template'])
and ConfigUpdate ('wysiwyg', $_GET['wysiwyg']) and ConfigUpdate ('global_comments', $InputValue2) and ConfigUpdate ('global_reads', $InputValue3)
and ConfigUpdate ('global_author', $InputValue4) and ConfigUpdate ('rewrite', $InputValue5) and ConfigUpdate ('global_voting', $InputValue8)
and ConfigUpdate ('global_linkers', $InputValue9) and ConfigUpdate ('imgbrowser', $_GET['imgbrowser']) and ConfigUpdate ('antispam', $InputValue0)
and ConfigUpdate ('language', $_GET['language']) and ConfigUpdate ('facebooklike', $InputValueA) and ConfigUpdate ('similararts', $InputValueB)
and ConfigUpdate ('sameoldarts', $InputValueC) and ConfigUpdate ('needConfirm', $InputValueD) and ConfigUpdate ('friends_mese', $InputValueE)
and ConfigUpdate ('twitterbutton', $InputValueF) and ConfigUpdate ('googleplus', $InputValueG)) $out .= GetIcon ('info', $translate['successact']);
else $out .= GetIcon ('error', $translate['failureact']);
break;





/*----- LIMITS -----*/



case 'limits':
if (ConfigUpdate ('list_arts', $_GET['list_arts']) and ConfigUpdate ('list_cats', $_GET['list_cats']) and ConfigUpdate ('list_imgs', $_GET['list_imgs'])
and ConfigUpdate ('list_coms', $_GET['list_coms']) and ConfigUpdate ('list_rss', $_GET['list_rss']) and ConfigUpdate ('menu_last_arts', $_GET['menu_last_arts'])
and ConfigUpdate ('menu_rand_arts', $_GET['menu_rand_arts']) and ConfigUpdate ('menu_last_cats', $_GET['menu_last_cats']) and ConfigUpdate ('menu_rand_cats', $_GET['menu_rand_cats'])
and ConfigUpdate ('menu_coms', $_GET['menu_coms']) and ConfigUpdate ('menu_top_arts', $_GET['menu_top_arts']) and ConfigUpdate ('menu_topvoted_arts', $_GET['menu_topvoted_arts'])
and ConfigUpdate ('menu_top_coms_arts', $_GET['menu_top_coms_arts'])) $out .= GetIcon ('info', $translate['successact']);
else $out .= GetIcon ('error', $translate['failureact']);
break;





/*----- CONNECT -----*/



case 'connect':
$InputValue = ($_GET['dbpass'] == '') ? $connect['pass'] : $_GET['dbpass'];
$ToWrite = "<?php

// " . date ('YmdHis') . "
\$connect = array (
	'server' => '".adjust($_GET['dbserver'])."',
	'user' => '".adjust($_GET['dbuser'])."',
	'pass' => '$InputValue',
	'dbname' => '".adjust($_GET['dbname'])."',
);
\$prefix = '".adjust($_GET['dbprefix'])."';";
@chmod ('_config.php', 0777);
if (@file_put_contents ('_config.php', $ToWrite) and ConfigUpdate ('conntype', $_GET['conntype']))
$out .= GetIcon ('info', $translate['successact']);
else $out .= GetIcon ('error', $translate['failureact']);
break;





/*----- ADMIN -----*/



case 'admin':
$InputValue1 = $_GET['son'] . ' ' . $_GET['sot'];
if (ConfigUpdate ('list_admin', $_GET['list_admin']) and ConfigUpdate ('list_stats', $_GET['list_stats']) and ConfigUpdate ('order', $InputValue1)
and ConfigUpdate ('admincolor', $_GET['admincolor']) and ConfigUpdate ('startpage', $_GET['startpage']) and ConfigUpdate ('loginback', $_GET['loginback']))
$out .= GetIcon ('info', $translate['successact']);
else $out .= GetIcon ('error', $translate['failureact']);
break;



}; /*--- Znovu načítanie nastavení ---*/
$query = @mysql_query ("SELECT * FROM {$prefix}_config");
while ($value = mysql_fetch_row ($query)) $_CONFIG[$value[0]] = setQ($value[1]);
}; switch ($_GET['mod']) {





/*----- Informácie o stránke -----*/



case 'site-info':
$CheckValue = array (
	($_CONFIG['title_reverse'] == 1) ? ' checked="checked"' : '',		# Aktuálna Stránka [sep] Názov Stránky (0)
	($_CONFIG['title_reverse'] == 0) ? ' checked="checked"' : '',		# Názov Stránky [sep] Aktuálna Stránka (1)
);
$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
$out = HeadIfPost ($translate['settings'] . ' &raquo; ' . $translate['settings.site']);
$out .= '<form action="admin.php?what=options&mod=site-info" method="post">
<div style="float:left;width:50%;">
<h2>' . $translate['settings.siteinfo'] . '</h2>
<fieldset>
<strong>' . $translate['settings.sitename'] . '</strong>
<input type="text" name="title" value="' . $_CONFIG['title'] . '" />
<strong>' . $translate['settings.sep'] . '</strong>
<input type="text" name="sep" value="'. $_CONFIG['sep'] . '" />
<strong>' . $translate['settings.titmod'] . '</strong>
<input type="radio" name="trev" value="1"'.$CheckValue[0].' id="trev1" /> <label for="trev1">' . $translate['settings.tma'] . '</label><br />
<input type="radio" name="trev" value="0"'.$CheckValue[1].' id="trev0" /> <label for="trev0">' . $translate['settings.tmb'] . '</label>
<strong>' . $translate['settings.sitedesc'] . '</strong>
<textarea name="desc" cols="4">' . $_CONFIG['desc'] . '</textarea>
<strong>' . $translate['settings.keywords'] . '</strong>
<input type="text" name="keys" value="' . $_CONFIG['keys'] . '" /> <em>' . $translate['settings.ksc'] . '</em>
</fieldset>
</div>

<div style="float:right;width:50%;">
<h2>' . $translate['settings.sitefoot'] . '</h2>
<fieldset>
<textarea id="footinfo" name="footinfo" rows="2">' . $_CONFIG['foot'] . '</textarea>
<script type="text/javascript">
	options = Texyla.configurator.forum ("footinfo");
	options.editorWidth = 450;
	options.toolbar = ["bold", "italic", "link", "ul", "ol", "sub", "sup", "emoticon", "symbol"];
	options.symbols = [["&", "&amp;"], ["©", "&copy;"], ["<", "&lt;"], [">", "&gt"]];
	options.submitButton = false;
	new Texyla (options);
</script>
</fieldset>
<h2>' . $translate['settings.blockip'] . '</h2>
<fieldset>
	<strong>' . $translate['settings.bititle'] . '</strong>
	<textarea name="block" rows="4">' . $_CONFIG['blockIP'] . '</textarea>
</fieldset>
</div>

<div class="cleaner">&nbsp;</div>

<input type="submit" value="' . $translate['save'] . '" name="ok" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>';
break;





/*----- Funkcie systému -----*/



case 'functions':
$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
$CheckValue = array (
	($_CONFIG['stats'] == 1) ? ' checked="checked"' : '',			# Štatistiky (0)
	($_CONFIG['global_comments'] == 1) ? ' checked="checked"' : '',		# Komentáre (1)
	($_CONFIG['global_reads'] == 1) ? ' checked="checked"' : '',		# Prečítania Článku (2)
	($_CONFIG['global_author'] == 1) ? ' checked="checked"' : '',		# Autor Článku (3)
	($_CONFIG['rewrite'] == 1) ? ' checked="checked"' : '',			# Mod Re-Write (4)
	(!file_exists ('.htaccess')) ? ' disabled="disabled"' : '',		# Mod Re-Write - Povolenie (5)
	($_CONFIG['admin_foot_link'] == 1) ? ' checked="checked"' : '',		# Odkaz na administráciu (6)
	($_CONFIG['global_voting'] == 1) ? ' checked="checked"' : '',		# Hodnotenie (7)
	($_CONFIG['global_linkers'] == 1) ? ' checked="checked"' : '',		# Služby na zbieranie odkazov (8)
	($_CONFIG['antispam'] == 1) ? ' checked="checked"' : '',		# Antispam ochrana (9)
	($_CONFIG['facebooklike'] == 1) ? ' checked="checked"' : '',		# Facebook like button (10)
	($_CONFIG['similararts'] == 1) ? ' checked="checked"' : '',		# Podobné články (11)
	($_CONFIG['sameoldarts'] == 1) ? ' checked="checked"' : '',		# Súbežné články (12)
	($_CONFIG['needConfirm'] == 1) ? ' checked="checked"' : '',		# Potrebné schálenie článkov (13)
	($_CONFIG['friends_mese'] == 1) ? ' checked="checked"' : '',		# OvaseMedia (14)
	($_CONFIG['twitterbutton'] == 1) ? ' checked="checked"' : '',		# Twitter Tweet Button (15)
	($_CONFIG['googleplus'] == 1) ? ' checked="checked"' : '',		# Google +1 Button (16)
);
$out = HeadIfPost ($translate['settings'] . ' &raquo; ' . $translate['settings.functions']);
$out .= '<form action="admin.php?what=options&mod=functions" method="post">
<h2>' . $translate['settings.sysfunctions'] . '</h2>
<fieldset>
<table width="100%"><tr><td valign="top">
<strong>' . $translate['settings.language'] . '</strong>
' . GetLangList ($_CONFIG['language']) . '
<strong>' . $translate['settings.theme'] . '</strong>
' . GetTemplateList ($_CONFIG['template']) . '</td><td valign="top">
<strong>' . $translate['settings.wysiwyg'] . '</strong>
' . GetWysiwygList ($_CONFIG['wysiwyg']) . '
<strong>' . $translate['settings.browser'] . '</strong>
' . GetBrowserList ($_CONFIG['imgbrowser']) . '</td><td valign="top">
<input type="checkbox" name="global_comments"' . $CheckValue[1] . ' id="i1" /> <label for="i1">' . $translate['settings.comms'] . '</label><br />
<input type="checkbox" name="stats"' . $CheckValue[0] . ' id="i6" /> <label for="i6">' . $translate['settings.stats'] . '</label><br />
<input type="checkbox" name="foot_link"' . $CheckValue[6] . ' id="i7" /> <label for="i7">' . $translate['settings.footlink'] . '</label><br />
<input type="checkbox" name="antispam"' . $CheckValue[9] . ' id="i8" /> <label for="i8">' . $translate['settings.antispam'] . '</label><br />
<input type="checkbox" name="needConfirm"' . $CheckValue[13] . ' id="i13" /> <label for="i13">' . $translate['settings.needConfirm'] . '</label><br />
<input type="checkbox" name="ovasemedia"' . $CheckValue[14] . ' id="i14" /> <label for="i14">' . $translate['ovase.allow'] . '</label>
</fieldset>
</td></tr></table>

<h2>' . $translate['settings.artviewer'] . '</h2>
<fieldset>
<div style="float:left;width:50%;">
<input type="checkbox" name="similararts"' . $CheckValue[11] . ' id="i11" /> <label for="i11">' . $translate['settings.similararts'] . '</label><br />
<input type="checkbox" name="sameoldarts"' . $CheckValue[12] . ' id="i12" /> <label for="i12">' . $translate['settings.sameoldarts'] . '</label><br />
<input type="checkbox" name="global_reads"' . $CheckValue[2] . ' id="i2" /> <label for="i2">' . $translate['settings.reads'] . '</label><br />
<input type="checkbox" name="global_author"' . $CheckValue[3] . ' id="i3" /> <label for="i3">' . $translate['settings.author'] . '</label><br />
<input type="checkbox" name="global_voting"' . $CheckValue[7] . ' id="i4" /> <label for="i4">' . $translate['settings.voting'] . '</label><br />
</div>
<div style="float:left;width:50%;">
<input type="checkbox" name="global_linkers"' . $CheckValue[8] . ' id="i5" /> <label for="i5">' . $translate['settings.link'] . '</label><br />
<input type="checkbox" name="fblike"' . $CheckValue[10] . ' id="i10" /> <label for="i10">' . $translate['settings.facebooklike'] . '</label><br />
<input type="checkbox" name="twitter"' . $CheckValue[15] . ' id="i15" /> <label for="i15">' . $translate['settings.twitter'] . '</label><br />
<input type="checkbox" name="googleplus"' . $CheckValue[16] . ' id="i16" /> <label for="i16">' . $translate['settings.googleplus'] . '</label>
</div>
</fieldset>

<h2>' . $translate['settings.mrw'] . '</h2>
<fieldset>
<input type="checkbox" name="rewrite"' . $CheckValue[4] . $CheckValue[5] . ' id="i9" /> <label for="i9">' . $translate['settings.mrwa'] . '</label>
' . GetIcon ('warning', $translate['settings.mrww']) . '
</fieldset>

<input type="submit" value="' . $translate['save'] . '" name="ok" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>';
break;





/*----- Limity na stránke -----*/



case 'limits':
$out = HeadIfPost ($translate['settings'] . ' &raquo; ' . $translate['settings.limits']);
$out .= '<form action="admin.php?what=options&mod=limits" method="post">

<h2>' . $translate['settings.syslims'] . '</h2>
<table cellspacing="7px">
	<tr><td><strong>' . $translate['articles'] . '</strong></td><td><input type="text" name="list_arts" value="' . $_CONFIG['list_arts'] . '" class="int3" /></td><td><em>' . $translate['settings.atitle'] . '</em></td></tr>
	<tr><td><strong>' . $translate['albums'] . '</strong></td><td><input type="text" name="list_cats" value="' . $_CONFIG['list_cats'] . '" class="int3" /></td><td><em>' . $translate['settings.ltitle'] . '</em></td></tr>
	<tr><td><strong>' . $translate['settings.media'] . '</strong></td><td><input type="text" name="list_imgs" value="' . $_CONFIG['list_imgs'] . '" class="int3" /></td><td><em></em>' . $translate['settings.mtitle'] . '</td></tr>
	<tr><td><strong>' . $translate['settings.comments'] . '</strong></td><td><input type="text" name="list_coms" value="' . $_CONFIG['list_coms'] . '" class="int3" /></td><td><em>' . $translate['settings.ctitle'] . '</em></td></tr>
	<tr><td><strong>' . $translate['settings.rss'] . '</strong></td><td><input type="text" name="list_rss" value="' . $_CONFIG['list_rss'] . '" class="int3" /></td><td><em>' . $translate['settings.rtitle'] . '</em></td></tr>
</table>

<h2>' . $translate['settings.gcmlims'] . '</h2>
<table cellspacing="7px">
	<tr><td><strong>' . $translate['menu.lastarts'] . '</strong></td><td><input type="text" name="menu_last_arts" value="' . $_CONFIG['menu_last_arts'] . '" class="int3" /></td><td><em>' . $translate['settings.lastarts'] . '</em></td></tr>
	<tr><td><strong>' . $translate['menu.randarts'] . '</strong></td><td><input type="text" name="menu_rand_arts" value="' . $_CONFIG['menu_rand_arts'] . '" class="int3" /></td><td><em>' . $translate['settings.randarts'] . '</em></td></tr>
	<tr><td><strong>' . $translate['menu.toparts'] . '</strong></td><td><input type="text" name="menu_top_arts" value="' . $_CONFIG['menu_top_arts'] . '" class="int3" /></td><td><em>' . $translate['settings.toparts'] . '</em></td></tr>
	<tr><td><strong>' . $translate['menu.tvarts'] . '</strong></td><td><input type="text" name="menu_topvoted_arts" value="' . $_CONFIG['menu_topvoted_arts'] . '" class="int3" /></td><td><em>' . $translate['settings.tvarts'] . '</em></td></tr>
	<tr><td><strong>' . $translate['menu.tcarts'] . '</strong></td><td><input type="text" name="menu_top_coms_arts" value="' . $_CONFIG['menu_top_coms_arts'] . '" class="int3" /></td><td><em>' . $translate['settings.tcarts'] . '</em></td></tr>
	<tr><td><strong>' . $translate['menu.lastalbs'] . '</strong></td><td><input type="text" name="menu_last_cats" value="' . $_CONFIG['menu_last_cats'] . '" class="int3" /></td><td><em>' . $translate['settings.lastalbs'] . '</em></td></tr>
	<tr><td><strong>' . $translate['menu.randalbs'] . '</strong></td><td><input type="text" name="menu_rand_cats" value="' . $_CONFIG['menu_rand_cats'] . '" class="int3" /></td><td><em>' . $translate['settings.randalbs'] . '</em></td></tr>
	<tr><td><strong>' . $translate['menu.lastcoms'] . '</strong></td><td><input type="text" name="menu_coms" value="' . $_CONFIG['menu_coms'] . '" class="int3" /></td><td><em>' . $translate['settings.lastcoms'] . '</em></td></tr>
</table>

<input type="submit" value="' . $translate['save'] . '" name="ok" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>';
break;





/*----- Pripojenie k databáze -----*/



case 'connect':
$CheckValue = ($_CONFIG['autologin'] == 0) ? '' : ' checked="checked"';
$checkvalue1 = ($_CONFIG['conntype'] == 1) ? '' : ' checked="checked"';
$checkvalue2 = ($_CONFIG['conntype'] == 0) ? '' : ' checked="checked"';
$out = HeadIfPost ($translate['settings'] . ' &raquo; ' . $translate['settings.database']);
$out .= '<form action="admin.php?what=options&mod=connect" method="post">
<h2>' . $translate['settings.database'] . '</h2>
<table cellspacing="7px">
	<tr><td><strong>' . $translate['settings.server'] . '</strong></td><td><input type="text" name="dbserver" value="' . $connect['server'] . '" class="width150" /></td><td><em>' . $translate['settings.tserver'] . '</em></td></tr>
	<tr><td><strong>' . $translate['settings.db'] . '</strong></td><td><input type="text" name="dbname" value="' . $connect['dbname'] . '" class="width150" /></td><td><em>' . $translate['settings.tdb'] . '</em></td></tr>
	<tr><td><strong>' . $translate['settings.user'] . '</strong></td><td><input type="text" name="dbuser" value="' . $connect['user'] . '" class="width150" /></td><td><em>' . $translate['settings.tuser'] . '</em></td></tr>
	<tr><td><strong>' . $translate['settings.pass'] . '</strong></td><td><input type="password" name="dbpass" class="width150" /></td><td><em>' . $translate['settings.tpass'] . '</em></td></tr>
	<tr><td><strong>' . $translate['settings.prefix'] . '</strong></td><td><input type="text" name="dbprefix" value="' . $prefix.'" class="width150" /></td><td><em>' . $translate['settings.tprefix'] . '</em></td></tr>
	<tr><td rowspan="3" valign="top"><strong>' . $translate['settings.connect'] . '</strong></td><td colspan="2"><input type="radio" name="conntype" value="0"'.$checkvalue1.' id="i1" /> <label for="i1">' . $translate['settings.servconn'] . '</label></td></tr>
	<tr><td colspan="2"><input type="radio" name="conntype" value="1"'.$checkvalue2.' id="i2" /> <label for="i2">' . $translate['settings.utf8conn'] . '</label></td></tr>
	<tr><td colspan="2"><em>' . $translate['notice'] . ': ' . $translate['settings.notice'] . '</em></td></tr>
</table>
<input type="submit" value="' . $translate['save'] . '" name="ok" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>';
break;





/*----- Administrácia -----*/



case 'admin':
$Ord = '<select name="son">';
	$order = explode (' ', $_CONFIG['order']);
	$Ord .= ($order[0] == 'nadpis') ? '<option value="nadpis" selected="selected">' . $translate['title'] . '</option><option value="id">' . $translate['arts.date'] . '</option>' : '<option value="nadpis">' . $translate['title'] . '</option><option value="id" selected="selected">' . $translate['arts.date'] . '</option>';
	$Ord .= '</select><br /><select name="sot">';
	$Ord .= ($order[1] == 'ASC') ? '<option value="ASC" selected="selected">' . $translate['settings.asc'] . '</option><option value="DESC">' . $translate['settings.desc'] . '</option>' : '<option value="ASC">' . $translate['settings.asc'] . '</option><option value="DESC" selected="selected">' . $translate['settings.desc'] . '</option>';
	$Ord .= '</select>';
$CheckValue = array (
	($_CONFIG['ahp_plugins'] == 1) ? ' checked="checked"' : '',			# Štatistiky (0)
	($_CONFIG['pluginsAllowed'] == 1) ? ' checked="checked"' : '',			# Štatistiky (0)
);
$out = HeadIfPost ($translate['settings'] . ' &raquo; ' . $translate['controlpanel']);
$out .= '<form action="admin.php?what=options&mod=admin" method="post">
<h2>' . $translate['settings'] . '</h2>
<fieldset>
<strong>' . $translate['settings.admincolor'] . '</strong>
<select name="admincolor">
 <option value="default">' . $translate['settings.defaultblue'] . '</option>' . n;
 $dir = opendir ('admin/remote/schemas');
 while ($file = readdir ($dir))
 {
  if (strpos ($file, '.theme.php') !== false)
  {
   $sname = substr ($file, 0, strrpos ($file, '.theme.php'));
   include_once ('admin/remote/schemas/' . $file);
   $out .= ' <option value="' . $sname . '"' . (($sname == $_CONFIG['admincolor']) ? ' selected="selected"' : '') . '>' . $themecolor . '</option>' . n;
  }
 };
 $out .= '</select>
<strong>' . $translate['settings.startpage'] . '</strong>
<select name="startpage">
 <option value="menu"' . (($_CONFIG['startpage'] == 'menu') ? ' selected="selected"' : '') . '>' . $translate['menu'] . '</option>
 <option value="apps"' . (($_CONFIG['startpage'] == 'apps') ? ' selected="selected"' : '') . '>' . $translate['apps'] . '</option>
 <option value="arts"' . (($_CONFIG['startpage'] == 'arts') ? ' selected="selected"' : '') . '>' . $translate['articles'] . '</option>
 <option value="albs"' . (($_CONFIG['startpage'] == 'albs') ? ' selected="selected"' : '') . '>' . $translate['albums'] . '</option>
 <option disabled="disabled">---</option>' . n;
$sql = mysql_query ("SELECT `fname`, `title` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `application` = 1 ORDER BY `title` ASC");
while ($app = mysql_fetch_assoc ($sql))
$out .= ' <option value="' . $app['fname'] . '"' . (($_CONFIG['startpage'] == $app['fname']) ? ' selected="selected"' : '') . '>' . $app['title'] . '</option>'.n;
$out .= '</select>
<strong>' . $translate['settings.orderby'] . '</strong>
'.$Ord.'
<strong>' . $translate['settings.lists'] . '</strong>
<input type="text" name="list_admin" value="' . $_CONFIG['list_admin'] . '" size="3" />
<strong>' . $translate['stats'] . '</strong>
<input type="text" name="list_stats" value="' . $_CONFIG['list_stats'] . '" size="3" />
<strong>' . $translate['settings.loginback'] . '</strong>
<input type="text" name="loginback" value="' . $_CONFIG['loginback'] . '" size="3" />
</fieldset>
<input type="submit" value="' . $translate['save'] . '" name="ok" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>';
break;};