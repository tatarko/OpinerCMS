<?php
if (!defined ('in')) exit ();
include ('admin/includes/TemplateClass.php');
$template = new TemplateClass ($_CONFIG['template']);
$out .= '<p class="right"><a href="admin.php?what=menu">' . TempIcon ('cat') . ' ' . $translate['menu'] . '</a> <a href="admin.php?what=sections">' . TempIcon ('cat') . ' ' . $translate['sections'] . '</a> <a href="admin.php?what=links">' . TempIcon ('cat') . ' ' . $translate['links'] . '</a> <a href="admin.php?what=art-categories">' . TempIcon ('cat') . ' ' . $translate['categories'] . '</a> <a href="admin.php?what=' . $what . '&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['menu.addbox'] . '</a></p>'.n;





/*----- Tabuľka -----*/

if (!isset ($_GET['mod'])) {
	$out = HeadIfPost ('Boxy menu', 'menubox');
	if (isset ($_GET['ok'], $_GET['menubox']) and is_array ($_GET['menubox'])) {
		$error = false;
		foreach ($_GET['menubox'] as $index => $value)
		$error =  ($error === false and mysql_query ("UPDATE {$prefix}_menu SET post = '$value' WHERE id = '$index' LIMIT 1;")) ? false : true;
		foreach ($_GET['sections'] as $index => $value)
		$error =  ($error === false and mysql_query ("UPDATE `{$prefix}_sec` SET `position` = " . adjust ($value, true) . " WHERE `id` = " . adjust ($index, true) . " LIMIT 1")) ? false : true;
		foreach ($_GET['links'] as $index => $value)
		$error =  ($error === false and mysql_query ("UPDATE `{$prefix}_links` SET `position` = " . adjust ($value, true) . " WHERE `id` = " . adjust ($index, true) . " LIMIT 1")) ? false : true;
		foreach ($_GET['categories'] as $index => $value)
		$error =  ($error === false and mysql_query ("UPDATE `{$prefix}_cats` SET `position` = " . adjust ($value, true) . " WHERE `id` = " . adjust ($index, true) . " LIMIT 1")) ? false : true;
		if (!$error)
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
	};
	$out .= '<form action="admin.php?what=menu" method="post">'.n;
	$query1 = @mysql_query ("SELECT DISTINCT kdeje FROM {$prefix}_menu ORDER BY kdeje ASC");
	while ($tab1 = @mysql_fetch_row ($query1)) {
		if (isset ($template->config["menu$tab1[0]"]['type'])) {
			$type = ($template->config["menu$tab1[0]"]['type'] == 'bar') ? $translate['menu.menu'] : $translate['menu.box'];
		} else $type = langrep ('menu.notinc', '<strong>' . langrep ('menu.name', $tab1[0]) . '</strong>');
		$out .= '<h2>' . langrep ('menu.name', $tab1[0]) . ' <em>(' . $type . ')</em></h2>'.n;
		$out .= '<table id="admintable" cellspacing="3px">'.n;
		$out .= '<tr><th>' . $translate['menu.os'] . '</th><th>' . $translate['title'] . '</th><th width="50px">' . $translate['action'] . '</th></tr>'.n;
		$sql = @mysql_query("SELECT id,post,text,obsah FROM {$prefix}_menu WHERE kdeje = $tab1[0] ORDER BY post ASC");
		if (mysql_num_rows ($sql) == 0) $out .= '<tr><td colspan="3">' . $translate['nocontent'] . '</td></tr>' . n;
		while ($tab = mysql_fetch_row($sql)) {
			@++$iid;
			if ($tab[3] == '<%mainmenu%>') {
				$secs = @mysql_query ("SELECT `id`, `nadpis`, `position` FROM `{$prefix}_sec` WHERE `msec` = 0 ORDER BY `position` ASC");
				$lins = @mysql_query ("SELECT `id`, `name`, `position` FROM `{$prefix}_links` WHERE `location` = 1 ORDER BY `position` ASC");
				$cats = @mysql_query ("SELECT `id`, `nadpis`, `position` FROM `{$prefix}_cats` WHERE `inmenu` = 1 ORDER BY `position` ASC");
				$rowspan = (mysql_num_rows ($secs) > 0 or mysql_num_rows ($lins) > 0 or mysql_num_rows ($cats) > 0) ? ' rowspan="' . (mysql_num_rows ($secs) + mysql_num_rows ($lins) + mysql_num_rows ($cats) + 2) . '" valign="top"' : '';
			} else $rowspan = '';
			$out .= '<tr><td class="alcen"' . $rowspan . '><input type="text" name="menubox['.$tab[0].']" value="'.$tab[1].'" class="smallint" /></td><td>'.$tab[2].'</td>';
			$out .= '<td><a href="?what=menu&mod=edit&id='.$tab[0].'">'.TempIcon ('edit').'</a><a href="?what=menu&mod=delete&id='.$tab[0].'">'.TempIcon ('delete').'</a></td></tr>'.n;
			if ($tab[3] == '<%mainmenu%>') {
				$mainmenu['d'] = ' <tr><td colspan="2"><em><a href="admin.php?what=sections&mod=add">' . $translate['sections.add'] . '</a> &bull; <a href="admin.php?what=links&mod=add">' . $translate['links.addlink'] . '</a> &bull; <a href="admin.php?what=art-categories&mod=add">' . $translate['cats.addcat'] . '</a></em></td></tr>' . n;
				while ($sec = mysql_fetch_assoc ($secs)) $mainmenu['a' . GetMilionNumber ($sec['position']) . GetMilionNumber ($sec['id']) . 'a'] = ' <tr><td><input type="text" name="sections[' . $sec['id'] . ']" value="' . $sec['position'] . '" class="smallint" /> <em>' . $sec['nadpis'] . '</em></td><td><a href="admin.php?what=sections&amp;mod=edit&amp;id=' . $sec['id'] . '">' . TempIcon ('edit') . '</a><a href="admin.php?what=sections&amp;mod=delete&amp;id=' . $sec['id'] . '">' . TempIcon ('delete') . '</a></td></tr>' . n;
				while ($lin = mysql_fetch_assoc ($lins)) $mainmenu['a' . GetMilionNumber ($lin['position']) . GetMilionNumber ($lin['id']) . 'c'] = ' <tr><td><input type="text" name="links[' . $lin['id'] . ']" value="' . $lin['position'] . '" class="smallint" /> <em>' . $lin['name'] . '</em></td><td><a href="admin.php?what=links&amp;mod=edit&amp;id=' . $lin['id'] . '">' . TempIcon ('edit') . '</a><a href="admin.php?what=links&amp;mod=delete&amp;id=' . $lin['id'] . '">' . TempIcon ('delete') . '</a></td></tr>' . n;
				while ($cat = mysql_fetch_assoc ($cats)) $mainmenu['a' . GetMilionNumber ($cat['position']) . GetMilionNumber ($cat['id']) . 'b'] = ' <tr><td><input type="text" name="categories[' . $cat['id'] . ']" value="' . $cat['position'] . '" class="smallint" /> <em>' . $cat['nadpis'] . '</em></td><td><a href="admin.php?what=art-categories&amp;mod=edit&amp;id=' . $cat['id'] . '">' . TempIcon ('edit') . '</a><a href="admin.php?what=art-categories&amp;mod=delete&amp;id=' . $cat['id'] . '">' . TempIcon ('delete') . '</a></td></tr>' . n;
				ksort ($mainmenu);
				$out .= implode ('', $mainmenu);
			};
		}; 
		$out .= '</table>';
		if (isset ($template->config["menu$tab1[0]"]['type']) and $template->config["menu$tab1[0]"]['type'] == 'bar') $out .= '<p style="font-size:10px;"><strong>' . $translate['notice'] . ':</strong> ' . $translate['menu.notice'] . '</p>';
	};
	$out .= '<input type="submit" name="ok" value="' . $translate['save'] . '" /><input type="reset" name="ok" value="' . $translate['reset'] . '" /></form>';



/*----- Pridávanie položky -----*/

} else if ($_GET['mod'] == 'add') {
	$out = HeadIfPost ($translate['menu.addbox'], 'menu-add-edit');
	if (isset ($_GET['ok'])) {
		if ($_GET['type'] == 'gencon') {
			switch ($_GET['gencon']) {
				case 'lastarts': $input = '[hcm]artsmenu[/hcm]'; break;
				case 'randarts': $input = '[hcm]artsrandmenu[/hcm]'; break;
				case 'toparts': $input = '[hcm]toparts[/hcm]'; break;
				case 'catlist': $input = '[hcm]catmenu[/hcm]'; break;
				case 'lastcats': $input = '[hcm]lastgall[/hcm]'; break;
				case 'randcats': $input = '[hcm]randgall[/hcm]'; break;
				case 'lastimgs': $input = '[hcm]lastimgs[/hcm]'; break;
				case 'randimgs': $input = '[hcm]randimgs[/hcm]'; break;
				case 'lastcoms': $input = '[hcm]lastcoms[/hcm]'; break;
				case 'links': $input = '[hcm]linksmenu[/hcm]'; break;
				case 'microblog': $input = '[hcm]microblog[/hcm]'; break;
				case 'fasttext': $input = '[hcm]fasttext[/hcm]'; break;
				case 'search': $input = '<%search%>'; break;
				case 'osearch': $input = '[hcm]oSearch[/hcm]'; break;
				case 'stats': $input = '[hcm]stats[/hcm]'; break;
				case 'topvotedarts': $input = '[hcm]topvotedarts[/hcm]'; break;
				case 'topcomsarts': $input = '[hcm]top-commented-articles[/hcm]'; break;
				default: $input = '<%mainmenu%>'; break;};
		} else $input = $_GET['owncon'];
		$mname = (isset ($_GET['mname'])) ? $_GET['mname'] : '';
		$jetobox = (array_search ($input, array ('[hcm]artsmenu[/hcm]', '[hcm]artsrandmenu[/hcm]', '[hcm]toparts[/hcm]', '[hcm]top-commented-articles[/hcm]',
		'[hcm]catmenu[/hcm]', '[hcm]lastgall[/hcm]', '[hcm]randgall[/hcm]', '[hcm]lastcoms[/hcm]', '[hcm]links[/hcm]', '[hcm]topvotedarts[/hcm]')) === false) ? 0 : 1;
		if (mysql_query ("INSERT INTO {$prefix}_menu VALUES (0, '".adjust($_GET["post"],true)."', $jetobox, '".adjust($_GET["kdeje"],true)."', '".adjust($_GET["text"])."', '$input', '$mname')")) {
			$IID = mysql_insert_id ();
			Header ('Location: ?what=menu&mod=edit&id='.$IID);
		} else $out .= GetIcon ('error', $translate['failureadd']);
	};
	for ($i = 1; $i <= $template->config['info']['count-menu']; ++$i) {
		$name = langrep ('menu.name', $i);
		$name .= ($template->config["menu$i"]['type'] == 'boxes') ? ' (' . $translate['menu.boxes.select'] . ')' : ' (' . $translate['menu.menu.select'] . ')';
		$select[] = '<option value="'.$i.'">' . $name . '</option>';
	};
	$select = '<select name="kdeje">' . n . implode (n, $select) . n . '</select>';
	$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
	$out .= '<form action="admin.php?what=menu&mod=add" method="post">
	<input type="hidden" name="page" value="admin" />
	<input type="hidden" name="what" value="' . $what . '" />
	<input type="hidden" name="mod" value="add" />
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['title'] . '</strong>
		<input type="text" name="text" />
		<strong>' . $translate['menu.insertin'] . '</strong>
		'.$select.'
		<strong>' . $translate['menu.position'] . '</strong>
		<input type="text" name="post" size="2" />
	</fieldset>

	<h2>' . $translate['menu.content'] . '</h2>
	<fieldset>
		<strong><input type="radio" name="type" value="gencon" id="gencon" /> <label for="gencon">' . $translate['menu.genc'] . ':</label></strong>
		<select name="gencon">
			<optgroup label="' . $translate['menu.menuable'] . '">
				<option value="mainemu">' . $translate['menu.mainmenu'] . '</option>
				<option value="lastarts">' . $translate['menu.lastarts'] . '</option>
				<option value="randarts">' . $translate['menu.randarts'] . '</option>
				<option value="toparts">' . $translate['menu.toparts'] . '</option>
				<option value="topvotedarts">' . $translate['menu.tvarts'] . '</option>
				<option value="topcomsarts">' . $translate['menu.tcarts'] . '</option>
				<option value="catlist">' . $translate['menu.catlist'] . '</option>
				<option value="lastcats">' . $translate['menu.lastalbs'] . '</option>
				<option value="randcats">' . $translate['menu.randalbs'] . '</option>
				<option value="lastcoms">' . $translate['menu.lastcoms'] . '</option>
				<option value="links">' . $translate['links'] . '</option>
			</optgroup>
			<optgroup label="' . $translate['menu.boxable'] . '">
				<option value="search">' . $translate['menu.search'] . '</option>
				<option value="osearch">' . $translate['menu.ovase'] . '</option>
				<option value="lastimgs">' . $translate['menu.lastimgs'] . '</option>
				<option value="randimgs">' . $translate['menu.randimgs'] . '</option>
				<option value="microblog">' . $_CONFIG['microblog_head'] . '</option>
				<option value="fasttext">' . $translate['menu.fasttext'] . '</option>
				<option value="stats">' . $translate['stats'] . '</option>
			</optgroup>
		</select>
		<strong><input type="radio" name="type" value="owncon" id="lowncon" checked="checked" /> <label for="lowncon">' . $translate['menu.ownc'] . '</label></strong>
		<textarea name="owncon" rows="5" cols="50" id="owncon"></textarea>
		<script type="text/javascript">
			options = Texyla.configurator.forum ("owncon");
			options.editorWidth = 450;
			options.toolbar = ["bold", "italic", "link", "ul", "ol", "sub", "sup", "emoticon", "symbol"];
			options.symbols = [["&", "&amp;"], ["©", "&copy;"], ["<", "&lt;"], [">", "&gt"]];
			options.submitButton = false;
			new Texyla (options);
		</script>
	</fieldset>

	<h2>' . $translate['settings'] . '</h2>
	<fieldset>
		<strong>[HTML] ID:</strong>
		<input type="text" name="mname" />
	</fieldset>
	<input type="submit" name="ok" value="' . $translate['add'] . '" />
</form>';





/*----- Mazanie -----*/

} else if ($_GET['mod'] == 'delete') {
	$out = HeadIfPost ($translate['menu.dropbox'], 'menu-delete');
	if (isset ($_GET['ok'])) {
		if ($_GET['ok'] == $translate['yes']) mysql_query ("DELETE FROM {$prefix}_menu WHERE id='".adjust($_GET["id"],true)."' LIMIT 1");
		Header ('Location: ?what=menu');
	};
	$out .= '<form action="admin.php?what=menu&mod=delete&id='.$_GET['id'].'" method="post">
	<p>' . $translate['sureact'] . '</p>
	<input type="submit" name="ok" value="' . $translate['yes'] . '" />
	<input type="submit" name="ok" value="' . $translate['no'] . '" />
	</form>';





/*----- Editacia -----*/

} else if ($_GET['mod'] == 'edit') {
	$out = HeadIfPost ($translate['menu.editbox'], 'menu-add-edit');
	if (isset ($_GET['ok'])) {
		if ($_GET['type'] == 'gencon') {
			switch ($_GET['gencon']) {
				case 'lastarts': $input = '[hcm]artsmenu[/hcm]'; break;
				case 'randarts': $input = '[hcm]artsrandmenu[/hcm]'; break;
				case 'toparts': $input = '[hcm]toparts[/hcm]'; break;
				case 'catlist': $input = '[hcm]catmenu[/hcm]'; break;
				case 'lastcats': $input = '[hcm]lastgall[/hcm]'; break;
				case 'randcats': $input = '[hcm]randgall[/hcm]'; break;
				case 'lastimgs': $input = '[hcm]lastimgs[/hcm]'; break;
				case 'randimgs': $input = '[hcm]randimgs[/hcm]'; break;
				case 'lastcoms': $input = '[hcm]lastcoms[/hcm]'; break;
				case 'links': $input = '[hcm]linksmenu[/hcm]'; break;
				case 'microblog': $input = '[hcm]microblog[/hcm]'; break;
				case 'fasttext': $input = '[hcm]fasttext[/hcm]'; break;
				case 'search': $input = '<%search%>'; break;
				case 'osearch': $input = '[hcm]oSearch[/hcm]'; break;
				case 'stats': $input = '[hcm]stats[/hcm]'; break;
				case 'topvotedarts': $input = '[hcm]topvotedarts[/hcm]'; break;
				case 'topcomsarts': $input = '[hcm]top-commented-articles[/hcm]'; break;
				default: $input = '<%mainmenu%>'; break;
			};
		} else $input = $_GET['owncon'];
		$mname = (isset ($_GET['mname'])) ? ", mname = '".adjust($_GET['mname'])."'" : '';
		$jetobox = (array_search ($input, array ('[hcm]artsmenu[/hcm]', '[hcm]artsrandmenu[/hcm]', '[hcm]toparts[/hcm]', '[hcm]top-commented-articles[/hcm]',
		'[hcm]catmenu[/hcm]', '[hcm]lastgall[/hcm]', '[hcm]randgall[/hcm]', '[hcm]lastcoms[/hcm]', '[hcm]links[/hcm]', '[hcm]topvotedarts[/hcm]')) === false) ? 0 : 1;
		if (@mysql_query ("UPDATE {$prefix}_menu SET jetobox = $jetobox, kdeje = '".adjust($_GET['kdeje'],true)."', post='".adjust($_GET["post"],true)."', text='".adjust($_GET["text"])."'{$mname}, obsah='$input' WHERE id='".adjust($_GET["id"],true)."' LIMIT 1"))
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
	};
	unset ($type);
	$info = @mysql_fetch_row (@mysql_query("SELECT post, text, obsah, mname, kdeje FROM {$prefix}_menu WHERE id='".adjust($_GET["id"],true)."'"));
	$check[] = ($info[2] == '<%mainmenu%>') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]artsmenu[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]artsrandmenu[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]catmenu[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]lastgall[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]randgall[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]lastimgs[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]randimgs[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]lastcoms[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]linksmenu[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]microblog[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]toparts[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]fasttext[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '<%search%>') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]topvotedarts[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]stats[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]oSearch[/hcm]') ? ' selected="selected"' : '';
	$check[] = ($info[2] == '[hcm]top-commented-articles[/hcm]') ? ' selected="selected"' : '';
	$tarea = (array_search (' selected="selected"', $check) === false) ? $info[2] : '';
	$type[] = (array_search (' selected="selected"', $check) === false) ? ' checked="checked"' : '';	// Vlastný obsah
	$type[] = (array_search (' selected="selected"', $check) === false) ? '' : ' checked="checked"';	// Generovaný obsah
	for ($i = 1; $i <= $template->config['info']['count-menu']; ++$i) {
		$name = langrep ('menu.name', $i);
		$name .= ($template->config["menu$i"]['type'] == 'boxes') ? ' (' . $translate['menu.boxes.select'] . ')' : ' (' . $translate['menu.menu.select'] . ')';
		$select[] = '<option value="'.$i.'"'.(($info[4]==$i)?' selected="selected"':'').'>' . $name . '</option>';
	};
	$select = '<select name="kdeje">' . n . implode (n, $select) . n . '</select>';
	$tohead = array_merge ($tohead, array ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>'));
	$out .= '<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['title'] . '</strong>
		<input type="text" name="text" value="'.setQ($info[1]).'" />
		<strong>' . $translate['menu.insertin'] . '</strong>
		'.$select.'
		<strong>' . $translate['menu.position'] . '</strong>
		<input type="text" name="post" size="2" value="'.$info[0].'" />
	</fieldset>

	<h2>' . $translate['menu.content'] . '</h2>
	<fieldset>
		<strong><input type="radio" name="type" value="gencon" id="gencon"'.$type[1].' /> <label for="gencon">' . $translate['menu.genc'] . '</label></strong>
		<select name="gencon">
			<optgroup label="' . $translate['menu.menuable'] . '">
				<option value="mainemu"'.$check[0].'>' . $translate['menu.mainmenu'] . '</option>
				<option value="lastarts"'.$check[1].'>' . $translate['menu.lastarts'] . '</option>
				<option value="randarts"'.$check[2].'>' . $translate['menu.randarts'] . '</option>
				<option value="toparts"'.$check[11].'>' . $translate['menu.toparts'] . '</option>
				<option value="topvotedarts"'.$check[14].'>' . $translate['menu.tvarts'] . '</option>
				<option value="topcomsarts"'.$check[17].'>' . $translate['menu.tcarts'] . '</option>
				<option value="catlist"'.$check[3].'>' . $translate['menu.catlist'] . '</option>
				<option value="lastcats"'.$check[4].'>' . $translate['menu.lastalbs'] . '</option>
				<option value="randcats"'.$check[5].'>' . $translate['menu.randalbs'] . '</option>
				<option value="lastcoms"'.$check[8].'>' . $translate['menu.lastcoms'] . '</option>
				<option value="links"'.$check[9].'>' . $translate['links'] . '</option>
			</optgroup>
			<optgroup label="' . $translate['menu.boxable'] . '">
				<option value="search"'.$check[13].'>' . $translate['menu.search'] . '</option>
				<option value="osearch"'.$check[16].'>' . $translate['menu.ovase'] . '</option>
				<option value="lastimgs"'.$check[6].'>' . $translate['menu.lastimgs'] . '</option>
				<option value="randimgs"'.$check[7].'>' . $translate['menu.randimgs'] . '</option>
				<option value="microblog"'.$check[10].'>' . $_CONFIG['microblog_head'] . '</option>
				<option value="fasttext"'.$check[12].'>' . $translate['menu.fasttext'] . '</option>
				<option value="stats"'.$check[15].'>' . $translate['stats'] . '</option>
			</optgroup>
		</select>
		<strong><input type="radio" name="type" value="owncon" id="lowncon"'.$type[0].' /> <label for="lowncon">' . $translate['menu.ownc'] . '</label></strong>
		<textarea name="owncon" rows="5" cols="50" id="owncon">'.$tarea.'</textarea>
		<script type="text/javascript">
			options = Texyla.configurator.forum ("owncon");
			options.editorWidth = 450;
			options.toolbar = ["bold", "italic", "link", "ul", "ol", "sub", "sup", "emoticon", "symbol"];
			options.symbols = [["&", "&amp;"], ["©", "&copy;"], ["<", "&lt;"], [">", "&gt"]];
			options.submitButton = false;
			new Texyla (options);
		</script>
	</fieldset>

	<h2>' . $translate['settings'] . '</h2>
	<fieldset>
		<strong>[HTML] ID:</strong>
		<input type="text" name="mname" value="'.$info[3].'" />
	</fieldset>
	<input type="submit" name="ok" value="' . $translate['save'] . '" />
</form>';
};
?>
