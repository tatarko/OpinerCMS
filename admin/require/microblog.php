<?php
if (!defined ('in')) exit ();
if (isset ($_REQUEST['return-page'])) $ReturnPage = $_REQUEST['return-page'];
else if ($_SERVER['HTTP_REFERER'] != '') $ReturnPage = $_SERVER['HTTP_REFERER'];
else $ReturnPage = './';
if (!isset ($_GET['mod'])) $_GET['mod'] = 'site-info';
$out .= '<p class="right">
 <a href="admin.php?what=options&amp;mod=site-info">' . TempIcon ('cat') . ' ' . $translate['settings.site'] . '</a>
 <a href="admin.php?what=options&amp;mod=functions">' . TempIcon ('cat') . ' ' . $translate['settings.functions'] . '</a>
 <a href="admin.php?what=options&amp;mod=limits">' . TempIcon ('cat') . ' ' . $translate['settings.limits'] . '</a>
 <a href="admin.php?what=options&amp;mod=admin">' . TempIcon ('cat') . ' ' . $translate['controlpanel'] . '</a>
 <a href="admin.php?what=microblog">' . TempIcon ('cat') . ' ' . $_CONFIG['microblog_head'] . '</a>
 <a href="admin.php?what=options&amp;mod=connect">' . TempIcon ('cat') . ' ' . $translate['settings.database'] . '</a>
</p>'.n;





/*----- Pridávanie postu -----*/

if (isset ($_REQUEST['mod'], $_REQUEST['blogpost']) and $_REQUEST['mod'] == 'add' and $_REQUEST['blogpost'] != '') {
	@mysql_query("INSERT INTO {$prefix}_microblog VALUES (0, ".USER.", NOW(), '".adjust($_REQUEST['blogpost'])."')");
	Header ('Location: ' . $ReturnPage);





/*----- Editovanie postu -----*/

} else if (isset ($_REQUEST['mod'], $_REQUEST['id']) and !isset ($_REQUEST['blogpost']) and $_REQUEST['mod'] == 'edit'
and ($info = mysql_fetch_row (mysql_query ("SELECT text FROM {$prefix}_microblog WHERE id = '".adjust($_REQUEST['id'],true)."'".FILTER." LIMIT 1")))) {
	$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
$out = headIfPost ($translate['microblog.edit']);
$out .= '<form action="admin.php?what=microblog&mod=edit&id=' . $_REQUEST['id'] . '" method="post">
<input type="hidden" name="return-page" value="' . $ReturnPage . '" />
<textarea id="blogpost" name="blogpost" rows="3" style="width:100%;">' . $info[0] . '</textarea>
<script type="text/javascript">
	opt = Texyla.configurator.forum ("blogpost");
	opt.editorWidth = 450;
	new Texyla (opt);
</script>
</form>'.n;





/*----- Editovanie postu -----*/

} else if (isset ($_REQUEST['mod'], $_REQUEST['id'], $_REQUEST['blogpost']) and $_REQUEST['blogpost'] != '' and $_REQUEST['mod'] == 'edit'
and ($info = mysql_fetch_row (mysql_query ("SELECT id FROM {$prefix}_microblog WHERE id = '".adjust($_REQUEST['id'],true)."'".FILTER." LIMIT 1")))) {
	mysql_query ("UPDATE {$prefix}_microblog SET text = '".adjust($_REQUEST['blogpost'])."' WHERE id = '".adjust($_REQUEST['id'],true)."' LIMIT 1");
	Header ('Location: ' . $ReturnPage);





/*----- Mazanie postu -----*/

} else if (isset ($_REQUEST['mod'], $_REQUEST['id']) and $_REQUEST['mod'] == 'delete') {
	mysql_query ("DELETE FROM {$prefix}_microblog WHERE id = '".adjust($_REQUEST['id'],true)."'".FILTER." LIMIT 1");
	Header ('Location: ' . $ReturnPage);





/*----- Nastavenia mikroblogovania -----*/

} else if (ADMIN) {
	$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
	if (isset ($_GET['head'], $_GET['list'], $_GET['menu'], $_GET['microblog'])) {
		if (ConfigUpdate ('microblog_head', $_GET['head'])
		and ConfigUpdate ('list_microblog', $_GET['list'])
		and ConfigUpdate ('menu_microblog', $_GET['menu'])
		and ConfigUpdate ('microblog', $_GET['microblog'])
		and ConfigUpdate ('twittername', $_GET['twittername'])
		and ConfigUpdate ('twittertime', $_GET['twittertime']))
		$out .= getIcon ('info', $translate['successact']);
		else $out .= getIcon ('error', $translate['failureact']);
	};
	$out = headIfPost ($translate['settings'] . ' &raquo; ' . $_CONFIG['microblog_head']) . '<form action="' . _SiteForm . '" method="post">
<h2>' . $translate['settings'] . '</h2>
<fieldset>
<strong>' . $translate['title'] . '</strong>
<input type="text" name="head" value="' . setQ ($_CONFIG['microblog_head']) . '" />
<strong>' . $translate['paging'] . '</strong>
<input type="text" name="list" value="' . setQ ($_CONFIG['list_microblog']) . '" />
<strong>' . $translate['links.genc'] . '</strong>
<input type="text" name="menu" value="' . setQ ($_CONFIG['menu_microblog']) . '" />
<strong>' . $translate['microblog.hometext'] . '</strong>
<textarea id="microblog" name="microblog" rows="3">' . $_CONFIG['microblog'] . '</textarea>
<script type="text/javascript">
	options = Texyla.configurator.forum ("microblog");
	options.editorWidth = 450;
	options.toolbar = ["bold", "italic", "link", "ul", "ol", "sub", "sup", "emoticon", "symbol"];
	options.symbols = [["&", "&amp;"], ["©", "&copy;"], ["<", "&lt;"], [">", "&gt"]];
	options.submitButton = false;
	new Texyla (options);
</script>
</fieldset>
<h2>' . langrep ('microblog.contw', '<a href="http://twitter.com/" target="_blank">Twitter</a>') . '</h2>
<fieldset>
<strong>' . $translate['redactors.nick'] . '</strong>
<input type="text" name="twittername" value="' . setQ ($_CONFIG['twittername']) . '" /><br />
<em>' . langrep ('microblog.twnt', '<a href="http://twitter.com/" target="_blank">Twitter</a>') . '</em>
<strong>' . $translate['microblog.twt'] . '</strong>
<input type="text" name="twittertime" value="' . setQ ($_CONFIG['twittertime']) . '" /><br />
<em>' . $translate['microblog.twtt'] . '</em>
</fieldset>
<input type="submit" value="' . $translate['save'] . '" />
<input type="reset" value="' . $translate['reset'] . '" />
</form>';
} else Header ('Location: ' . $ReturnPage);
?>