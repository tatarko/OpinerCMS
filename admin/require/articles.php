<?php
if(!defined("in"))exit();
$out .= '<p class="right">
 <a href="admin.php?what=articles">' . TempIcon ('cat') . ' ' . $translate['articles'] . '</a>';
if ($_USER_INFO['albums']) $out .= '
 <a href="admin.php?what=gallery">' . TempIcon ('cat') . ' ' . $translate['albums'] . '</a>'; $out .='
 <a href="admin.php?what=polls">' . TempIcon ('cat') . ' ' . $translate['polls'] . '</a>
 <a href="admin.php?what=articles&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['arts.addart'] . '</a>
</p>'.n;





/*------ Úvodná tabuľka -----*/

if(!isset($_GET["mod"])) {
	$where = (isset ($_GET['cat'])) ? " AND `cat` = '".adjust($_GET['cat'], true)."' OR `cat2` = '".adjust($_GET['cat'], true)."' OR `cat3` = '".adjust($_GET['cat'], true)."'" : '';
	$out = HeadIfPost ($translate['articles'], 'arts');
	$sql = @mysql_query("SELECT `id`,`nadpis`,`showing`,DATE_FORMAT(`added`,'%d.%m %Y, %H:%i'),`reads`,`cat`,`cat2`,`cat3`,`autor` FROM `{$prefix}_clanky` WHERE 1=1$where".FILTER." ORDER BY {$_CONFIG['order']} LIMIT ".(($pag-1)*$limit).", $limit");
	$out.='<table id="admintable" cellspacing="3px">
	<tr><th width="30px">ID</th><th>' . $translate['title'] . '</th><th width="140px">' . $translate['redactors.redactor'] . '</th><th width="250px">' . $translate['arts.cat'] . '</th><th width="120px">' . $translate['arts.date'] . '</th><th width="30px">' . $translate['arts.readed'] . '</th><th width="70px">' . $translate['action'] . '</th></tr>'.n;
	while ($tab=mysql_fetch_row ($sql)) {
		@++$ii;
		$out .= '<tr><td>'.$tab[0].'</td>';
		$out .= ($tab[2] == 1) ? '<td>'.$tab[1].'</td>' : '<td><b><small><i>'.$tab[1].' (archív)</i></small></b></td>';
		$out .= '<td>' . GetAuthorName ($tab[8]) . '</td><td>'.GetCatsList($tab[5],$tab[6],$tab[7]).'</td><td>'.$tab[3].'</td><td>'.$tab[4].'x</td><td><a href="?what=articles&mod=edit&id='.$tab[0].'">'.TempIcon('edit').'</a><a href="?what=articles&mod=delete&id='.$tab[0].'">'.TempIcon('delete').'</a>';
		if ($tab[2] != 0) $out .= '<a href="'.rwl('clanok', $tab[0] . '-' . SeoTitle($tab[1])).'" target="_blank">'.TempIcon('blank').'</a>';
		$out .= '</td></tr>'.n;
	};
	if (!isset ($ii)) $out .= '<tr><td colspan="7">' . $translate['nocontent'] . '</td></tr>'.n;
	$out .= '</table>'.n;
	$out .= GetPagesList ('clanky', 'articles', "1=1$where".FILTER);




/*----- Pridávanie / editovanie článku -----*/

} else if ($_GET['mod'] == 'add' or $_GET['mod'] == 'edit') {
	$mode = ($_GET['mod'] == 'add') ? 'add' : 'edit';
	if (!(($mode == 'edit' and $info = @mysql_fetch_row (@mysql_query ("SELECT `nadpis`, `text`, `perex`, `cat`, `cat2`, `cat3`, `showing`, `coms`, `imagelink`, `added`, `tags`, `autor`, `album` FROM {$prefix}_clanky WHERE id = '".adjust($_GET['id'])."'".FILTER." LIMIT 1")))
	or $mode == 'add')) Header ('Location: admin.php?what=articles');
	$out = HeadIfPost ((($mode == 'add') ? $translate['arts.addart'] : $translate['arts.editart']), 'arts-add-edit');
	if (isset ($_GET['ok'])) {
		
		/* Ukladanie do DB */

		if ($_GET['nadpis'] != '' and $_GET['category1'] != '' and $_GET['perex'] != '') {
			$seo = SeoTitle ($_GET['nadpis']);
			$showing = (isset ($_REQUEST['showing'])) ? 1 : 0;
			$comments = (isset ($_REQUEST['comments'])) ? 1 : 0;
			$author = (ADMIN and isset ($_REQUEST['author'])) ? adjust ($_REQUEST['author'], true) : USER;
			if (isset ($_FILES['image']) and $_FILES['image']['error'] == 0 and strpos ($_FILES['image']['type'], 'image/') !== false) {
				$name = 'store/icons/' . time() . substr ($_FILES['image']['name'], strrpos ($_FILES['image']['name'], '.'));
				if (@move_uploaded_file ($_FILES['image']['tmp_name'], $name)) $_GET['imagelink'] = $name;
			} else if (isset ($_GET['imageselect']) and $_GET['imageselect'] != 'null') $_GET['imagelink'] = $_GET['imageselect'];
			if (isset ($_GET['actualdt'])) $_GET['datetime'] = date ('Y-m-d H:i:s');
			if ($mode == 'edit') $id = GetIntFromGetValue ($_GET['id']);
			$confirmed = (!ADMIN and ($_CONFIG['needConfirm'] == 1 or $_USER_INFO["needConfirm"] == 1)) ? 0 : 1;
			$query = ($mode == 'add') ?
			"INSERT INTO {$prefix}_clanky VALUES (0, $author, '".adjust($_GET['nadpis'])."', '$seo', '".adjust($_GET['imagelink'])."', '".adjust($_GET['text'])."', '".adjust($_GET['perex'])."', '".adjust($_GET['datetime'])."', '".adjust($_GET['category1'],true)."', '".adjust($_GET['category2'],true)."', '".adjust($_GET['category3'],true)."', $showing, $confirmed, $comments, 0, '" . adjustTags ($_REQUEST['tags']) . "', " . adjust ($_GET['album'], true) . ")":
			"UPDATE {$prefix}_clanky SET `autor` = $author, nadpis = '".adjust($_GET['nadpis'])."', seo = '$seo', imagelink = '".adjust($_GET['imagelink'])."', text = '".adjust($_GET['text'])."', perex = '".adjust($_GET['perex'])."', cat = '".adjust($_GET['category1'],true)."', cat2 = '".adjust($_GET['category2'],true)."', cat3 = '".adjust($_GET['category3'],true)."', showing = $showing, tags = '" . adjustTags ($_REQUEST['tags']) . "', coms = $comments, added = '".adjust($_GET['datetime'])."', `album` = " . adjust ($_GET['album'], true) . " WHERE id = $id".FILTER." LIMIT 1";
			if (mysql_query($query)) {
				if ($mode == 'add') {
					$IID = mysql_insert_id ();
					Header ('Location: ?what=articles&mod=edit&id=' . $IID);
				} else $out .= GetIcon ('info', $translate['successact']);
			} else $out .= GetIcon ('error', $translate['failureact']);
		} else $out .= GetIcon ('error', $translate['nofill']);


		/*--- Vykonávanie akcií ---*/
		if (isset ($_GET['delcoms']) and $mode == 'edit') @mysql_query ("DELETE FROM {$prefix}_comments WHERE kde = 'clanok_$id'");
		if (isset ($_GET['delvoting']) and $mode == 'edit') @mysql_query ("DELETE FROM {$prefix}_iplog WHERE what = 'hodnotenie_art_$id'");
		if (isset ($_GET['delreads']) and $mode == 'edit') @mysql_query ("UPDATE {$prefix}_clanky SET `reads` = 0 WHERE id = '$id' LIMIT 1");
	};


/*--- Štandardné hodnoty ---*/
if ($mode == 'edit') $DefaultVars = array ('nadpis' => $info[0], 'perex' => $info[2], 'text' => $info[1], 'imagelink' => $info[8],
'cat1' => $info[3], 'cat2' => $info[4], 'cat3' => $info[5], 'datetime' => $info[9],
'showing' => (($info[6] == 1) ? ' checked="checked"' : ''), 'tags' => $info[10],
'coms' => (($info[7] == 1) ? ' checked="checked"' : ''), 'author' => $info[11], 'album' => $info[12]);
else $DefaultVars =  array ('nadpis' => '', 'perex' => '', 'imagelink' => '',
'text' => '', 'cat1' => 1, 'cat2' => 0, 'cat3' => 0, 'showing' => ' checked="checked"',
'coms' => ' checked="checked"', 'datetime' => date ('Y-m-d H:i:s'), 'tags' => '', 'author' => USER, 'album' => 0);
if (isset ($_GET['ok']) and $mode == 'edit') $DefaultVars =  array (
'nadpis' => $_GET['nadpis'], 'perex' => $_GET['perex'], 'text' => $_GET['text'], 'imagelink' => $_GET['imagelink'],
'cat1' => $_GET['category1'], 'cat2' => $_GET['category2'], 'cat3' => $_GET['category3'],  
'showing' => ((isset ($_GET['showing'])) ? ' checked="checked"' : ''), 'tags' => $_REQUEST['tags'],
'coms' => ((isset ($_GET['comments'])) ? ' checked="checked"' : ''), 'datetime' => $_GET['datetime'], 'album' => $_GET['album'],
'author' => (isset ($_REQUEST['author'])) ? adjust ($_REQUEST['author'], true) : USER);


/*--- Výber kategórií ---*/
$sql = mysql_query ("SELECT id,nadpis FROM {$prefix}_cats");
while ($tab = mysql_fetch_row($sql)) $kategorie[$tab[0]] = '<option value="'.$tab[0].'">'.$tab[1].'</option>';
@$kategorie1 = $kategorie;
$kategorie[0] = '<option value="0">&lt;' . $translate['none'] . '&gt;</option>';
ksort ($kategorie);
$kategorie2 = $kategorie;
$kategorie3 = $kategorie;
if ($mode == 'edit') {
if ($DefaultVars['cat2'] == '') $DefaultVars['cat2'] = '0';
if ($DefaultVars['cat3'] == '') $DefaultVars['cat3'] = '0';
$kategorie1[$DefaultVars['cat1']] = str_replace ('<option', '<option selected="selected"', $kategorie1[$DefaultVars['cat1']]);
$kategorie2[$DefaultVars['cat2']] = str_replace ('<option', '<option selected="selected"', $kategorie2[$DefaultVars['cat2']]);
$kategorie3[$DefaultVars['cat3']] = str_replace ('<option', '<option selected="selected"', $kategorie3[$DefaultVars['cat3']]);};
@$kategorie1 = implode ("\n", $kategorie1);
$kategorie2 = implode ("\n", $kategorie2);
$kategorie3 = implode ("\n", $kategorie3);
$ImageSelect = '<select name="imageselect">
<option value="null">(' . $translate['none'] . ')</option>'.n;
$sqla = @mysql_query ("SELECT DISTINCT `imagelink` as `imagelink`, `nadpis` FROM `{$prefix}_clanky` ORDER BY `id` ASC");
while ($temp = @mysql_fetch_row ($sqla)) $ImageSelect .= '	<option value="' . $temp[0] . '">' . $temp[1] . '</option>'.n;
$ImageSelect .= '</select><br />'.n;
$AlbumSelect = '<select name="album">
<option value="0">(' . $translate['none'] . ')</option>'.n;
$sqla = @mysql_query ("SELECT `id`, `nadpis` FROM `{$prefix}_gall` ORDER BY `id` DESC");
while ($temp = @mysql_fetch_row ($sqla)) $AlbumSelect .= ' <option value="' . $temp[0] . '"' . (($DefaultVars['album'] == $temp[0]) ? ' selected="selected"' : '') . '>' . $temp[1] . '</option>'.n;
$AlbumSelect .= '</select><br />'.n;

/*--- Výber autora článku ---*/
if (ADMIN
and $sqlb = mysql_query ("SELECT `id`, `name` FROM `{$prefix}_moderators` ORDER BY `name` ASC")
and mysql_num_rows ($sqlb) != 0){
 $author_select = '<strong>' . $translate['arts.author'] . '</strong>
<select name="author">'.n;
 while ($user = mysql_fetch_assoc ($sqlb))
 $author_select .= ' <option value="' . $user['id'] . '"' . (($DefaultVars['author'] == $user['id']) ? ' selected="selected"' : '') . '>' . $user['name'] . '</option>'.n;
 $author_select .= '</select>'.n;
}else $author_select = '';
 

/*--- Výstup ---*/
$out.='<form action="admin.php?' . $_SERVER["QUERY_STRING"] . '" method="post" enctype="multipart/form-data">
<h2>' . $translate['maininfo'] . '</h2>
<fieldset>
<table><tr><td><strong>' . $translate['title'] . '</strong>';
$out .= '<input type="text" name="nadpis" size="35" style="margin:3px;" value="' . setQ ($DefaultVars['nadpis']) . '" /></td>
<td width="100%">' .  $author_select . '</td></tr><tr><td colspan="2">
<strong>' . $translate['categories'] . '</strong>
<select name="category1" style="margin-right:5px;">
' . $kategorie1 . '
</select>
<select name="category2" style="margin-right:5px;">
' . $kategorie2 . '
</select>
<select name="category3">
' . $kategorie3 . '
</select>
</td></tr></table>
</fieldset>

<h2>' . $translate['arts.perex'] . '</h2>
<fieldset>'.n;
$wy = new wdriver('perex', $DefaultVars['perex']);
$out .= $wy->Draw().n;
$out .= '</fieldset>

<h2>' . $translate['arts.fullart'] . '</h2>
<fieldset>'.n;
$wy = new wdriver('text', $DefaultVars['text']);
$out .= $wy->Draw().n;
$out .= '</fieldset>

<h2>' . $translate['settings'] . '</h2>
<fieldset>
<div style="float:left;width:50%;">
<strong>' . $translate['arts.date'] . '</strong>
<input type="text" name="datetime" size="20" value="' . $DefaultVars['datetime'] . '" /> (RRRR-MM-DD HH:MM:SS)<br />
<input type="checkbox" name="actualdt"'.(($mode=='add')?' checked="checked"':'').' /> ' . $translate['arts.actual'] . '
<strong>' . $translate['arts.tags'] . '</strong>
<input type="text" name="tags" size="20" value="' . $DefaultVars['tags'] . '" />
<strong>' . $translate['othersettings'] . '</strong>
<input type="checkbox" name="showing"' . $DefaultVars['showing'] . ' /> ' . $translate['arts.show'] . '<br />
<input type="checkbox" name="comments"' . $DefaultVars['coms'] . ' /> ' . $translate['arts.coms'];
if ($mode == 'edit') $out .= '<br />
<input type="checkbox" name="delcoms" /> ' . $translate['arts.delcoms'] . '<br />
<input type="checkbox" name="delvoting" /> ' . $translate['arts.delvotes'] . '<br />
<input type="checkbox" name="delreads" /> ' . $translate['arts.delreads'];
$out .= '</div>
<div style="float:right;width:50%;">
<strong>' . $translate['arts.image'] . '</strong>
<em>' . $translate['arts.image.url'] . '</em><br />
<input type="text" name="imagelink" size="35" value="' . $DefaultVars['imagelink'] . '" /><br />
<em>' . $translate['arts.image.reuse'] . '</em><br />
' . $ImageSelect . '
<em>' . $translate['arts.image.upload'] . '</em><br />
<input type="file" name="image" />
<strong>' . $translate['arts.attach.album'] . '</strong>
' . $AlbumSelect . '
</div>
</fieldset>

<input type="submit" value="' . $translate['save'] . '" name="ok" />
</form>';
if (@mysql_num_rows ($sql) == 0) $out = '<p>' . langrep ('arts.nocats', '<a href="?page=admin&what=art-categories&mod=add">' . $translate['cats.addcat'] . '</a>') . '</p>';






/*----- Mazanie článku -----*/

} else if ($_GET['mod'] == 'delete') {
	$out = HeadIfPost ($translate['arts.dropart'], 'arts-delete');
	if (isset ($_GET['ok'])) {
		if ($_GET['ok'] != $translate['yes']) Header('Location: ?what=articles');
		else {
			if (@mysql_query ("DELETE FROM {$prefix}_clanky WHERE id='".adjust($_GET["id"],true)."'".FILTER." LIMIT 1")
			and @mysql_query ("DELETE FROM {$prefix}_comments WHERE kde='clanok_".adjust($_GET["id"],true)."'"))
			$out .= GetIcon ('info', $translate['successact']);
			else $out .= GetIcon ('error', $translate['failureact']);
		};
	} else {
		$out .= '<form action="admin.php?' . $_SERVER["QUERY_STRING"] . '" method="post">
	<p>' . $translate['sureact'] . '</p>
	<input type="checkbox" checked="checked" disabled="disabled" /> ' . $translate['arts.delcoms'] . '<br />
	<input type="submit" name="ok" value="' . $translate['yes'] . '" />
	<input type="submit" name="ok" value="' . $translate['no'] . '" />
</form>';};};
?>