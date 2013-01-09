<?php
if (!defined ('in')) exit ();
function GetIntoSections ($id, $sub, $check = 0, $msec = 0) {
	global $prefix, $translate;
	$id += 0;
	$sub += 0;
	$msec += 0;
	$check += 0;
	$SubString = '';
	for ($i = 1; $i <= $sub; ++$i) $SubString .= '&gt;';
	if ($SubString != '') $SubString .= ' ';
	$query = @mysql_query ("SELECT `id`, `nadpis` FROM `{$prefix}_sec` WHERE `msec` = $id AND id != $check ORDER BY id ASC");
	if (@mysql_num_rows ($query) > 0) {
		while ($value = @mysql_fetch_row ($query)) {
			$string = '<option value="' . $value[0] . '"';
			$string .= ($msec == $value[0]) ? ' selected="selected"' : '';
			$string .= '>' . $SubString . $value[1] . ' (' . langrep ('sections.choose', $value[0]) . ')</option>';
			$array[] = $string;
			$into = GetIntoSections ($value[0], $sub + 1, $check, $msec);
			if ($into !== false) $array = array_merge ($array, $into);
		};
		return $array;
	} else return false;
};
if (ADMIN) $out .= '<p class="right"><a href="admin.php?what=menu">' . TempIcon ('cat') . ' ' . $translate['menu'] . '</a> <a href="admin.php?what=sections">' . TempIcon ('cat') . ' ' . $translate['sections'] . '</a> <a href="admin.php?what=links">' . TempIcon ('cat') . ' ' . $translate['links'] . '</a> <a href="admin.php?what=art-categories">' . TempIcon ('cat') . ' ' . $translate['categories'] . '</a> <a href="admin.php?what=' . $what . '&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['sections.add'] . '</a></p>'.n;





/*----- Úvodná tabuľka -----*/

if(!isset ($_GET['mod']) or $_GET['mod'] == 'settings'){
	$tohead[] = '<script src="admin/remote/js/sections.js" type="text/javascript"></script>';
	$out = HeadIfPost ($translate['sections']);
	if (ADMIN and isset ($_GET['mod'])) {
		if (ConfigUpdate ('homepage', $_GET['homp']))
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
		$config = mysql_fetch_row (mysql_query ("SELECT hodnota FROM {$prefix}_config WHERE nazov = 'homepage' LIMIT 1"));
		$_CONFIG['homepage'] = $config[0];
	};
	if (ADMIN and isset ($_REQUEST['allow'], $_REQUEST['redactor'])) {
		if(mysql_query ("INSERT INTO `{$prefix}_iplog` VALUES(0, 'managesection', " . adjust ($_REQUEST['allow'], true) . ", " . adjust ($_REQUEST['redactor'], true) . ");"))
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
	};
	if (ADMIN and isset ($_REQUEST['deny'])) {
		if(mysql_query ("DELETE FROM `{$prefix}_iplog` WHERE `id` = " . adjust ($_REQUEST['deny'], true) . " LIMIT 1"))
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
	};
	$out .= '<table id="admintable" cellspacing="3px">
	<tr><th width="30px">ID</th><th width="30px">' . $translate['sections.poss'] . '</th><th>' . $translate['title'] . '</th><th width="70px">' . $translate['action'] . '</th></tr>' . n;
	$where = isset ($allowedsections) ? 'WHERE ' . implode (' OR ', $allowedsections) : '';
	$sql = @mysql_query("SELECT `nadpis`, `seo`, `id`, `msec`, `position` FROM `{$prefix}_sec`" . $where . " ORDER BY {$_CONFIG['order']} LIMIT ".(($pag-1)*$limit).", $limit");
	if (mysql_num_rows ($sql) > 0) {
	while ($tab = mysql_fetch_row ($sql)){
		$out .= '<tr><td>'.$tab[2].'</td><td>'.$tab[4].'</td><td>';
		if ($tab[2] == $_CONFIG['homepage']) $out .= '<b>' . $tab[0] . '</b> [' . $translate['sections.startsec'] . ']';
		else if ($tab[3] == 0) $out .= '<b>' . $tab[0] . '</b> [' . $translate['sections.inmainmenu'] . ']';
		else $out .= $tab[0];
		$out .= '</td><td><a href="?what=sections&mod=edit&id='.$tab[2].'">'.TempIcon('edit').'</a>' . ((ADMIN)?'<a href="?what=sections&mod=delete&id='.$tab[2].'">'.TempIcon('delete').'</a>':'') . '<a href="'.rwl('sekcia',"$tab[2]-$tab[1]").'" target="_blank">'.TempIcon('blank').'</a></td></tr>'.n;
		if (ADMIN){
		$out .= '<tr><td colspan="2"><em>' . $translate['sections.redactors'] . '</em></td><td>';
		$query = @mysql_query ("SELECT `a`.`id`, `b`.`name`, `b`.`id` as `mod` FROM `{$prefix}_iplog` as `a`, `{$prefix}_moderators` as `b` WHERE `a`.`hodnota` = `b`.`id` AND `a`.`ip` = 'managesection' AND `a`.`what` = {$tab[2]} ORDER BY `b`.`name` ASC");
		$mods = array ();
		while ($info = @mysql_fetch_assoc ($query))
		{
		        $mods[] = '`id` != ' . $info['mod'];
			$out .= $info['name'] . ' <a href="?what=sections&deny=' . $info['id'] . '">' . TempIcon ('delete') . '</a> ';
		}
		if (@mysql_num_rows ($query) == 0) $out .= $translate['sections.noeditors'];
		$out .= '<form action="admin.php?what=sections&allow=' . $tab[2] . '" method="post" id="form' . $tab[2] . '" class="displaynone"><select name="redactor">'.n;
		$query = @mysql_query ("SELECT `id`, `name` FROM `{$prefix}_moderators` WHERE `isadmin` = 0" . ((!empty ($mods)) ? ' AND ' . implode(' AND ', $mods) : '') . " ORDER BY `name` ASC");
		while ($info = @mysql_fetch_assoc ($query))
		$out .= '<option value="' . $info['id'] . '">' . $info['name'] . '</option>'.n;
		$out .= '</select><input type="submit" value="' . $translate['add'] . '" /></form></td><td><a href="#form' . $tab[2] . '" class="addeditor">' . TempIcon('add') . ' ' . $translate['sections.addeditor'] . '</a></td></tr>'.n;
		};
	};} else $out .= '<tr><td colspan="4">' . $translate['nocontent'] . '</td></tr>' . n;
	$out .= '</table>';
	$out .= GetPagesList ('sec', 'sections');
	if (ADMIN){
	$SecSelect = '<select name="homp"><option value="#">(' . $translate['lastarts'] . ')</option>';
	$sql = mysql_query("SELECT nadpis,id FROM {$prefix}_sec ORDER BY nadpis ASC");
	while ($tab = mysql_fetch_row($sql)) {
		$SecSelect .= "<option value='$tab[1]'";
		$SecSelect .= ($_CONFIG['homepage'] == $tab[1]) ? " selected='selected'" : '';
		$SecSelect .= ">$tab[0]</option>";};
	$SecSelect .= '</select>';
	$out .= '<form action="admin.php?what=sections&mod=settings" method="post">
	<fieldset>
		<h2>' . $translate['settings'] . '</h2>
		' . $translate['sections.startsection'] . ' &nbsp; ' . $SecSelect . ' &nbsp; &nbsp; &nbsp; <input type="submit" name="ok" value="' . $translate['save'] . '" />
	</fieldset>
	</form>';
	};





/*----- Pridávanie sekcie -----*/

} else if ($_GET['mod'] == 'add') {
	$out = HeadIfPost ($translate['sections.add']);
	if (isset($_GET['text'])) {
		if ($_GET['nadpis'] != '' and $_GET['text'] != '') {
			$com = (isset ($_GET['com'])) ? 1 : 0;
			if (mysql_query("INSERT INTO {$prefix}_sec VALUES (0, '" . adjust ($_GET['nadpis']) . "', '" . adjust ($_GET['popis']) . "', '".SeoTitle($_GET["nadpis"])."','".adjust($_GET["text"])."','$com','".adjust($_GET['msec'],true)."','".adjust($_GET['position'],true)."')")) {
				$IID = mysql_insert_id ();
				if(!ADMIN) mysql_query("INSERT INTO `{$prefix}_iplog` VALUES(0, 'managesection', $IID, " . USER . ");");
				Header ('Location: ?what=sections&mod=edit&id=' . $IID);
			} else $out .= GetIcon ('error', $translate['failureadd']);
		} else $out .= GetIcon ('error', $translate['nofill']);
	};
	$InSelect[] = '<select name="msec">';
	$InSelect[] = '<option value="-1">' . $translate['sections.whome'] . '</option>';
	$InSelect[] = '<option value="0">' . $translate['sections.mainmenu'] . '</option>';
	$InSelect[] = '<option value="-1">----------</option>';
	$InSelect = array_merge ($InSelect, GetIntoSections (0, 0));
	$InSelect[] = '</select>';
	$InSelect = implode ("\n\t\t", $InSelect);
	$out .= '<form action="admin.php?' .$_SERVER['QUERY_STRING']. '" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['title'] . '</strong>
		<input type="text" name="nadpis"'.((isset($_GET['nadpis']))?" value=\"{$_GET['nadpis']}\"":'').' />
		<strong>' . $translate['sections.home'] . '</strong>
		' . $InSelect . '
		<strong>' . $translate['sections.position'] . '</strong>
		<input type="text" name="position"'.((isset($_GET['position']))?" value=\"{$_GET['position']}\"":'').' />
		<strong>' . $translate['settings'] . '</strong>
		<input type="checkbox" name="com" /> ' . $translate['sections.comments'] . '
		<strong>' . $translate['description'] . '</strong>
		<textarea name="popis" rows="2">' . ((isset ($_GET['nadpis'])) ? $_GET['nadpis'] : '') . '</textarea>
	</fieldset>

	<h2>' . $translate['sections.content'] . '</h2>
	<fieldset>'.n;
$wy = (isset($_GET['text'])) ? new wdriver('text', $_GET['text']) : new wdriver('text');
$out .= $wy->Draw();
$out .= '	</fieldset>
	<input type="submit" value="' . $translate['add'] . '" />
</form>';





/*----- Editácie sekcie -----*/

} else if ($_GET['mod'] == 'edit') {
	$out = HeadIfPost ($translate['sections.edit']);
	if (isset($_GET['text']) and $_GET['nadpis'] != '' and $_GET['text'] != '') {
		$seo = SeoTitle($_GET["nadpis"]);
		$com = (isset ($_GET['com'])) ? 1 : 0;
		if (mysql_query("UPDATE {$prefix}_sec SET popis='".adjust($_GET['popis'])."',nadpis='".adjust($_GET["nadpis"])."',text='".adjust($_GET["text"])."',com='$com',msec='".adjust($_GET['msec'],true)."',seo='$seo', position='".adjust($_GET['position'],true)."' WHERE id='".adjust($_GET["id"],true)."'")) {
			$out .= GetIcon ('info', $translate['successact']);
			if ($info = mysql_fetch_row (mysql_query ("SELECT seo FROM {$prefix}_sec WHERE id = '".adjust($_GET['id'])."' LIMIT 1"))
			and $info[0] == $_CONFIG['homepage']) ConfigUpdate ('homepage', $seo);
		} else $out .= GetIcon ('error', $translate['failureact']);
	};
	if ($info = mysql_fetch_row(mysql_query("SELECT nadpis,text,com,msec,position,popis FROM {$prefix}_sec WHERE id='".adjust($_GET["id"],true)."' LIMIT 1"))) {
	$com = ($info[2] == 1) ? 'checked="checked" ' : '';
	$InSelect[] = '<select name="msec">';
	$InSelect[] = '<option value="-1">' . $translate['sections.whome'] . '</option>';
	$InSelect[] = ($info[3] == 0) ? '<option value="0" selected="selected">' . $translate['sections.mainmenu'] . '</option>' : '<option value="0">' . $translate['sections.mainmenu'] . '</option>';
	$InSelect[] = '<option value="-1">----------</option>';
	$ar = GetIntoSections (0, 0, $_GET['id'], $info[3]);
	if (is_array ($ar) and count ($ar) > 0)	$InSelect = array_merge ($InSelect, $ar);
	$InSelect[] = '</select>';
	$InSelect = implode ("\n\t\t", $InSelect);
	$out .= '<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['title'] . '</strong>
		<input type="text" name="nadpis" value="'.setQ($info[0]).'" />
		<strong>' . $translate['sections.home'] . '</strong>
		' . $InSelect . '
		<strong>' . $translate['sections.position'] . '</strong>
		<input type="text" name="position" value="'.$info[4].'" />
		<strong>' . $translate['settings'] . '</strong>
		<input type="checkbox" name="com" '.$com.'/> ' . $translate['sections.comments'] . '
		<strong>' . $translate['description'] . '</strong>
		<textarea name="popis" rows="2">' . $info[5] . '</textarea>
	</fieldset>

	<h2>' . $translate['sections.content'] . '</h2>	
	<fieldset>'.n;
$wy = new wdriver('text', $info[1]);
$out .= $wy->Draw();
$out .= '	</fieldset>
	<input type="submit" value="' . $translate['save'] . '" />
	<input type="reset" value="' . $translate['reset'] . '" />
</form>';
	} else GetIcon ('error', 'Sekcia nebola nájdená');





/*----- Mazanie sekcie -----*/

} else if (ADMIN and $_GET['mod'] == 'delete') {
	$out = HeadIfPost ($translate['sections.drop']);
	if ($_GET['id'] != $_CONFIG['homepage']) {
		if (isset ($_GET['ok'])) {
			if ($_GET['ok'] != $translate['yes']) Header('Location: ?what=sections');
			if (@mysql_query ("DELETE FROM {$prefix}_sec WHERE id='".adjust($_GET["id"],true)."' LIMIT 1")
			and @mysql_query ("DELETE FROM {$prefix}_comments WHERE kde='sec_".adjust($_GET["id"],true)."'"))
			Header ('Location: ?what=sections');
			else $out .= GetIcon ('error', $translate['failureact']);
		};
		$out .= '<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post">
	<fieldset>
		<strong>' . $translate['sureact'] . '</strong>
		<input type="checkbox" checked="checked" disabled="disabled" /> ' . $translate['sections.delcoms'] . '<br />
		<input type="submit" name="ok" value="' . $translate['yes'] . '" />
		<input type="submit" name="ok" value="' . $translate['no'] . '" />
	</fieldset>
</form>';
	} else $out .= GetIcon ('error', $translate['sections.dna']);
};
?>