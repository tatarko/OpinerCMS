<?php
if (!defined ('in')) exit ();
$out .= '<p class="right"><a href="admin.php?what=menu">' . TempIcon ('cat') . ' ' . $translate['menu'] . '</a> <a href="admin.php?what=sections">' . TempIcon ('cat') . ' ' . $translate['sections'] . '</a> <a href="admin.php?what=links">' . TempIcon ('cat') . ' ' . $translate['links'] . '</a> <a href="admin.php?what=art-categories">' . TempIcon ('cat') . ' ' . $translate['categories'] . '</a> <a href="admin.php?what=' . $what . '&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['links.addlink'] . '</a></p>'.n;





/*----- Tabuľka -----*/

if (!isset ($_GET['mod'])) {
	$out = HeadIfPost ($translate['links'], 'links');
	$sql = @mysql_query("SELECT * FROM {$prefix}_links ORDER BY location ASC, position ASC");
	$out .= '<table id="admintable" cellspacing="3px">
	<tr><th width="30px">ID</th><th width="30px">' . $translate['links.poss'] . '</th><th>' . $translate['links.link'] . '</th><th width="50px">' . $translate['action'] . '</th></tr>' . n;
	while ($tab = @mysql_fetch_row($sql)) {
		@++$iid;
		$out .= '<tr><td>' . $tab[0] . '</td><td>' . $tab[6] . '</td><td>' . $tab[4] . '</td><td><a href="?what=links&mod=edit&id=' . $tab[0] . '">'.TempIcon ('edit').'</a><a href="?what=links&mod=delete&id=' . $tab[0] . '">'.TempIcon ('delete').'</a></td></tr>' . n;
	};
	if(!isset ($iid)) $out .= '<tr><td colspan="4">' . $translate['nocontent'] . '</td></tr>' . n;
	$out .= '</table>';





/*----- Pridávanie položky -----*/

} else if ($_GET['mod'] == 'add') {
	$out = HeadIfPost ($translate['links.addlink'], 'links-add-edit');
	if (isset ($_GET['ok'])) {
		$href = ($_GET['href'] == '$link$') ? $_GET['url'] : (($_GET['href'] == '$app$') ? $_GET['capp'] : $_GET['href']);
		$target = (isset ($_GET['target'])) ? '_blank' : '_top';
		if (@mysql_query ("INSERT INTO {$prefix}_links VALUES (0, '$href', '".adjust($_GET["title"])."', '$target', '".adjust($_GET["name"])."', '".adjust($_GET['location'],true)."', '".adjust($_GET['position'],true)."')")) {
			$IID = mysql_insert_id ();
			Header ('Location: ?what=links&mod=edit&id=' . $IID);
		} else $out .= GetIcon ('error', $translate['failureadd']);
	} else {
	$out .= '<form action="admin.php?what=links&mod=add" method="post">
<h2>' . $translate['maininfo'] . '</h2>
<fieldset>
 <strong>' . $translate['links.text'] . '</strong>
 <input type="text" name="name" />
 <strong>' . $translate['description'] . '</strong>
 <input type="text" name="title" />
 <strong>' . $translate['links.url'] . '</strong>
 <input type="radio" name="href" value="[home]" id="h1" checked="checked" /> <label for="h1">' . $translate['links.homesite'] . '</label><br />
 <input type="radio" name="href" value="[archive]" id="h2" /> <label for="h2">' . $translate['links.archive'] . '</label><br />
 <input type="radio" name="href" value="[catlist]" id="h8" /> <label for="h8">' . $translate['links.calendar'] . '</label><br />
 <input type="radio" name="href" value="[gallery]" id="h3" /> <label for="h3">' . $translate['links.gallery'] . '</label><br />
 <input type="radio" name="href" value="[gbook]" id="h4" /> <label for="h4">' . $translate['links.gbook'] . '</label><br />
 <input type="radio" name="href" value="[sitemap]" id="h5" /> <label for="h5">' . $translate['links.sitemap'] . '</label><br />
 <input type="radio" name="href" value="[rss]" id="h6" /> <label for="h6">' . $translate['links.rss'] . '</label><br />' . n;
 $apps = @mysql_query ("SELECT `id`, `title`, `fname` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `plugin` = 1 ORDER BY `installed` ASC");
 if (@mysql_num_rows ($apps) > 0) {
  $out .= ' <input type="radio" name="href" value="$app$" id="h9" /> <label for="h9">' . $translate['links.app'] . ':</label>
  <select name="capp">' . n;
  while ($app = mysql_fetch_assoc ($apps)) $out .= '   <option value="[app:' . $app['fname'] . ']">' . $app['title'] . '</option>' . n;
  $out .= '  </select><br />' . n;
 };
 $out .= ' <input type="radio" name="href" value="$link$" id="h7" /> <label for="h7">' . $translate['links.ownurl'] . ':</label> <input type="text" name="url" />
</fieldset>

<h2>' . $translate['settings'] . '</h2>
<fieldset>
 <strong>' . $translate['links.home'] . '</strong>
 <input type="radio" name="location" value="0" id="t0" checked="checked" /> <label for="t0">' . $translate['links.genc'] . '</label><br />
 <input type="radio" name="location" value="1" id="t1" /> <label for="t1">' . $translate['links.mainmenu'] . '</label><br />
 <input type="radio" name="location" value="2" id="t2" /> <label for="t2">' . $translate['links.hcm'] . '</label>
 <strong>' . $translate['links.position'] . '</strong>
 <input type="text" name="position" />
 <strong>' . $translate['othersettings'] . '</strong>
 <input type="checkbox" name="target" id="target" /> <label for="target">' . $translate['links.newwin'] . '</label>
</fieldset>
<input type="submit" name="ok" value="' . $translate['add'] . '" />
</form>';};





/*----- Mazanie -----*/

} else if ($_GET['mod'] == 'delete') {
	$out = HeadIfPost ($translate['links.droplink'], 'links-delete');
	if (isset ($_GET['ok'])) {
		if ($_GET['ok'] == $translate['yes']) {
			if (!@mysql_query ("DELETE FROM `{$prefix}_links` WHERE `id` = '" . adjust ($_GET['id'], true) . "' LIMIT 1"))
			$out .= getIcon ('info', $translate['successact']);
			else $out .= getIcon ('error', $translate['failureact']);
		} else Header ('Location: ?what=links');
	} else {
	$out .= '<form action="admin.php?what=links&mod=delete&id='.$_GET['id'].'" method="post">
	<p>' . $translate['sureact'] . '</p>
	<input type="submit" name="ok" value="' . $translate['yes'] . '" />
	<input type="submit" name="ok" value="' . $translate['no'] . '" />
</form>';};





/*----- Editacia -----*/

	} else if ($_GET['mod'] == 'edit') {
	$out = HeadIfPost ($translate['links.editlink'], 'links-add-edit');
	if (isset ($_GET['ok'])) {
		$href = ($_GET['href'] == '$link$') ? $_GET['url'] : (($_GET['href'] == '$app$') ? $_GET['capp'] : $_GET['href']);
		$target = (isset ($_GET['target'])) ? '_blank' : '_top';
		if (@mysql_query ("UPDATE {$prefix}_links SET href = '$href', title = '".adjust($_GET['title'])."', target = '$target', name = '".adjust($_GET['name'])."', location = '".adjust($_GET['location'],true)."', position = '".adjust($_GET['position'],true)."' WHERE id='".adjust($_GET["id"],true)."' LIMIT 1"))
		$out .= getIcon ('info', $translate['successact']);
		else $out .= getIcon ('error', $translate['failureact']);
	};
	$info = @mysql_fetch_row (@mysql_query("SELECT href, title, target, name, location, position FROM {$prefix}_links WHERE id='".adjust($_GET["id"],true)."'"));
	$value = ($info[2] == '_blank') ? ' checked="checked"' : '';
	$array = array ('[home]', '[archive]', '[catlist]', '[gallery]', '[qbook]', '[sitemap]', '[rss]');
	foreach ($array as $hod) $chv[] = ($info[0] == $hod) ? ' checked="checked"' : '';
	$chv[] = (preg_match ("#\[app\:([a-zA-Z0-9_]+?)\]#ius", $info [0])) ? ' checked="checked"' : '';
	$ch = (array_search (' checked="checked"', $chv) === false) ? array (' checked="checked"', $info[0]) : array ('', ''); 
	$array = array (0, 1, 2);
	foreach ($array as $hod) $cv[] = ($info[4] == $hod) ? ' checked="checked"' : '';
	$out .= '<form action="admin.php?what=links&mod=edit&id='.$_GET['id'].'" method="post">
	<table width="100%"><tr><td valign="top">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['links.text'] . '</strong>
		<input type="text" name="name" value="' . setQ($info[3]) . '"/>
		<strong>' . $translate['description'] . '</strong>
		<input type="text" name="title" value="' . setQ($info[1]) . '" />
		<strong>' . $translate['links.url'] . '</strong>
		<input type="radio" name="href" value="[home]" id="h1"'.$chv[0].' /> <label for="h1">' . $translate['links.homesite'] . '</label><br />
		<input type="radio" name="href" value="[catlist]" id="h2"'.$chv[1].' /> <label for="h2">' . $translate['links.archive'] . '</label><br />
		<input type="radio" name="href" value="[catlist]" id="h8"'.$chv[2].' /> <label for="h8">' . $translate['links.calendar'] . '</label><br />
		<input type="radio" name="href" value="[gallery]" id="h3"'.$chv[3].' /> <label for="h3">' . $translate['links.gallery'] . '</label><br />
		<input type="radio" name="href" value="[gbook]" id="h4"'.$chv[4].' /> <label for="h4">' . $translate['links.gbook'] . '</label><br />
		<input type="radio" name="href" value="[sitemap]" id="h5"'.$chv[5].' /> <label for="h5">' . $translate['links.sitemap'] . '</label><br />
		<input type="radio" name="href" value="[rss]" id="h6"'.$chv[6].' /> <label for="h6">' . $translate['links.rss'] . '</label><br />' . n;
 $apps = @mysql_query ("SELECT `id`, `title`, `fname` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `plugin` = 1 ORDER BY `installed` ASC");
 if (@mysql_num_rows ($apps) > 0) {
  $out .= ' <input type="radio" name="href" value="$app$" id="h9"'.$chv[7].' /> <label for="h9">' . $translate['links.app'] . ':</label>
  <select name="capp">' . n;
  while ($app = mysql_fetch_assoc ($apps)) $out .= '   <option value="[app:' . $app['fname'] . ']"' . (('[app:' . $app['fname'] . ']' == $info[0]) ? ' selected="selected"' : '') . '>' . $app['title'] . '</option>' . n;
  $out .= '  </select><br />' . n;
 };
 $out .= ' 
		<input type="radio" name="href" value="$link$" id="h7"'.$ch[0].' /> <label for="h7">' . $translate['links.ownurl'] . ':</label> <input type="text" name="url" value="'.setQ($ch[1]).'" />
	</fieldset>
	</td><td valign="top">
	<h2>' . $translate['settings'] . '</h2>
	<fieldset>
		<strong>' . $translate['links.home'] . '</strong>
		<input type="radio" name="location" value="0" id="t0"'.$cv[0].' /> <label for="t0">' . $translate['links.genc'] . '</label><br />
		<input type="radio" name="location" value="1" id="t1"'.$cv[1].' /> <label for="t1">' . $translate['links.mainmenu'] . '</label><br />
		<input type="radio" name="location" value="2" id="t2"'.$cv[2].' /> <label for="t2">' . $translate['links.hcm'] . '</label>
		<strong>' . $translate['links.position'] . '</strong>
		<input type="text" name="position" value="' . $info[5] . '" />
		<strong>' . $translate['othersettings'] . '</strong>
		<input type="checkbox" name="target" id="target"' . $value . ' /> <label for="target">' . $translate['links.newwin'] . '</label>
	</fieldset>
	</td></tr></table>
	<input type="submit" name="ok" value="' . $translate['save'] . '" />
	<input type="reset" name="ok" value="' . $translate['reset'] . '" />
</form>';};
?>