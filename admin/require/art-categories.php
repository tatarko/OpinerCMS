<?php
if(!defined("in"))exit();
$out .= '<p class="right">
 <a href="admin.php?what=menu">' . TempIcon ('cat') . ' ' . $translate['menu'] . '</a>
 <a href="admin.php?what=sections">' . TempIcon ('cat') . ' ' . $translate['sections'] . '</a>
 <a href="admin.php?what=links">' . TempIcon ('cat') . ' ' . $translate['links'] . '</a>
 <a href="admin.php?what=art-categories">' . TempIcon ('cat') . ' ' . $translate['categories'] . '</a>
 <a href="admin.php?what=' . $what . '&amp;mod=move">' . TempIcon ('move') . ' ' . $translate['cats.move'] . '</a>
 <a href="admin.php?what=' . $what . '&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['cats.addcat'] . '</a>
</p>'.n;





/*----- Úvodná tabuľka -----*/

if (!isset ($_GET['mod'])) {
$out = HeadIfPost ($translate['categories']);
$out.='<table id="admintable" cellspacing="3px">
<tr><th width="30px">ID</th><th>' . $translate['title'] . '</th><th width="100px">' . $translate['action'] . '</th></tr>'.n;
$sql = @mysql_query("SELECT id,nadpis,showing FROM {$prefix}_cats ORDER BY {$_CONFIG['order']} LIMIT ".(($pag-1)*$limit).", $limit");
if (mysql_num_rows ($sql) > 0) {while ($tab = mysql_fetch_row($sql)) {
	$out .= '<tr><td>'.$tab[0].'</td><td>';
	$out .= ($tab[2] == 1) ? $tab[1] : '<i>'.$tab[1].'</i>';
	$out .= '</td><td><a href="?what=art-categories&mod=edit&id='.$tab[0].'">'.TempIcon('edit').'</a>
	<a href="?what=art-categories&mod=delete&id='.$tab[0].'">'.TempIcon('delete').'</a>
	<a href="?what=articles&cat='.$tab[0].'">'.TempIcon('cat').'</a>
	<a href="./'.rwl('kategoria',SeoTitle($tab[0].'-'.$tab[1])).'" target="_blank">'.TempIcon('blank').'</a></td></tr>'.n;};
} else $out .= '<tr><td colspan="2">' . $translate['nocontent'] . '</td></tr>'.n;
$out .= '</table>'.n;
$out .= GetPagesList ('cats', 'art-categories');





/*----- Pridávanie kategórie -----*/

}else if($_GET["mod"]=="add"){
	$out = HeadIfPost ($translate['cats.addcat']);
	if(isset($_GET["nadpis"]) and $_GET['nadpis'] != '') {
		$showing = (isset ($_GET['showing'])) ? 1 : 0;
		$inmenu = (isset ($_GET['inmenu'])) ? 1 : 0;
		$seo = SeoTitle ($_GET['nadpis']);
		if (mysql_query("INSERT INTO {$prefix}_cats VALUES ('0','".adjust($_GET["nadpis"])."','".adjust($_GET['popis'])."','$seo','".adjust($_GET["imagelink"])."',$showing,$inmenu,".adjust($_GET["position"],true).")")) {
			$IID = mysql_insert_id ();
			Header ('Location: ?what=art-categories&mod=edit&id=' . $IID);
		} else $out .= GetIcon ('error', $translate['failureadd']);
	};
	$out .= '<form action="admin.php?what=art-categories&mod=add" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<table><tr><td valign="top">
	<fieldset>
		<strong>' . $translate['title'] . '</strong>
		<input type="text" name="nadpis" size="45" />
		<strong>' . $translate['cats.image'] . '</strong>
		<input type="text" name="imagelink" size="45" />
		<strong>' . $translate['cats.position'] . '</strong>
		<input type="text" name="position" size="45" />
	</fieldset>
	</td><td valign="top">
	<fieldset>
		<strong>' . $translate['description'] . '</strong>
		<textarea name="popis"></textarea><br />
		<input type="checkbox" name="showing" checked="checked" /> ' . $translate['cats.show'] . '<br />
		<input type="checkbox" name="inmenu" /> ' . $translate['cats.inmenu'] . '
	</fieldset>
	</td></tr></table>
	<input type="submit" value="' . $translate['add'] . '" />
</form>';





/*----- Editacia kategórie -----*/

}else if($_GET["mod"]=="edit"){
	$out = HeadIfPost ($translate['cats.editcat']);
	if (isset($_GET['nadpis']) and $_GET['nadpis'] != '') {
		$showing = (isset ($_GET['showing'])) ? 1 : 0;
		$inmenu = (isset ($_GET['inmenu'])) ? 1 : 0;
		$seo =  SeoTitle ($_GET['nadpis']);
		if (mysql_query("UPDATE {$prefix}_cats SET popis = '".adjust($_GET['popis'])."', nadpis = '".adjust($_GET["nadpis"])."', showing = $showing, skr = '$seo', imagelink = '".adjust($_GET['imagelink'])."', `inmenu` = $inmenu, `position` = ".adjust($_GET['position'], true)." WHERE id='".adjust($_GET["id"], true)."' LIMIT 1"))
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
	};
	if ($info = mysql_fetch_row(mysql_query("SELECT nadpis, showing, skr, imagelink, popis, inmenu, position FROM {$prefix}_cats WHERE id='".adjust($_GET["id"], true)."'"))) {
		$ch = ($info[1] == 1) ? 'checked="checked" ' : '';
		$im = ($info[5] == 1) ? 'checked="checked" ' : '';
		$out .= '<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<table><tr><td valign="top">
	<fieldset>
		<strong>' . $translate['title'] . '</strong>
		<input type="text" name="nadpis" size="45" value="'.setQ($info[0]).'" />
		<strong>' . $translate['cats.image'] . '</strong>
		<input type="text" name="imagelink" size="45" value="'.$info[3].'" />
		<strong>' . $translate['cats.position'] . '</strong>
		<input type="text" name="position" size="45" value="'.$info[6].'" />
	</fieldset>
	</td><td valign="top">
	<fieldset>
		<strong>' . $translate['description'] . '</strong>
		<textarea name="popis">' . $info[4] . '</textarea><br />
		<input type="checkbox" name="showing" '.$ch.'/> ' . $translate['cats.show'] . '<br />
		<input type="checkbox" name="inmenu" '.$im.' /> ' . $translate['cats.inmenu'] . '
	</fieldset>
	</td></tr></table>
	<input type="submit" value="' . $translate['save'] . '" />
</form>';
	} else Header ('Location: ?what=art-categories');





/*----- Mazanie kategórie -----*/

} else if ($_GET['mod'] == 'delete') {
	$out = HeadIfPost ($translate['cats.dropcat']);
	if (isset ($_GET['ok'])) {
		if ($_GET['ok'] != $translate['yes']) Header ('Location: ?what=art-categories');
		else {
			$sql = mysql_query ("SELECT id FROM {$prefix}_clanky WHERE cat = '".adjust($_GET["id"], true)."'");
			if (mysql_num_rows ($sql) > 0) {
				while ($id = mysql_fetch_row ($sql)) $IDs[] = "kde = 'clanok_$id[0]'";
				$IDy = implode (' OR ', $IDs);
				$COMs = (mysql_query ("DELETE FROM {$prefix}_comments WHERE $IDy")) ? true : false;
			} else $COMs = true;
			if (mysql_query ("DELETE FROM {$prefix}_cats WHERE id = '".adjust($_GET["id"],true)."' LIMIT 1")
			and mysql_query ("DELETE FROM {$prefix}_clanky WHERE cat = '".adjust($_GET["id"],true)."'")
			and mysql_query ("UPDATE {$prefix}_clanky SET cat2 = 0 WHERE cat2 = '".adjust($_GET["id"], true)."'")
			and mysql_query ("UPDATE {$prefix}_clanky SET cat3 = 0 WHERE cat3 = '".adjust($_GET["id"], true)."'")
			and $COMs) $out .= GetIcon ('info', $translate['successact']);
			else $out .= GetIcon ('info', $translate['failureact']);
		};
	} else {
		$out .= '<form action="admin.php?' . $_SERVER["QUERY_STRING"] . '" method="post">
	<p>' . $translate['sureact'] . '</p>
	<input type="checkbox" checked="checked" disabled="disabled" /> ' . langrep ('cats.delarts', '<a href="?what=art-categories&mod=move">' . $translate['cats.move'] . '</a>') . '<br />
	<input type="submit" name="ok" value="' . $translate['yes'] . '" />
	<input type="submit" name="ok" value="' . $translate['no'] . '" />
</form>';};





/*----- Presúvanie článkov -----*/

} else if ($_GET['mod'] == 'move') {
	$out = HeadIfPost ($translate['cats.move']);
	if (isset ($_GET['from'], $_GET['to']) and $_GET['from'] != $_GET['to']) {
		if (@mysql_query ("UPDATE {$prefix}_clanky SET cat = '".adjust($_GET["to"], true)."' WHERE cat = '".adjust($_GET["from"], true)."'")
		and @mysql_query ("UPDATE {$prefix}_clanky SET cat2 = '".adjust($_GET["to"],true)."' WHERE cat2 = '".adjust($_GET["from"],true)."'")
		and @mysql_query ("UPDATE {$prefix}_clanky SET cat3 = '".adjust($_GET["to"],true)."' WHERE cat3 = '".adjust($_GET["from"],true)."'"))
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
	};
	$query = @mysql_query ("SELECT id, nadpis FROM {$prefix}_cats ORDER BY nadpis ASC");
	if (mysql_num_rows ($query) > 0) {
		while ($tab = mysql_fetch_row ($query)) $select[] = '<option value="' . $tab[0] . '">' . $tab[1] . '</option>';
		$select = implode ("\n", $select) . substr ("\n", 0, -1);
	} else $select = '';
	$out .= '<form action="admin.php?' . $_SERVER["QUERY_STRING"] . '" method="post">
	<strong>' . $translate['cats.from'] . '</strong><br />
	<select name="from">'.$select.'</select><br />
	<strong>' . $translate['cats.to'] . '</strong><br />
	<select name="to">'.$select.'</select><br />
	<input type="submit" value="' . $translate['cats.smove'] . '" />
</form>';
};
?>