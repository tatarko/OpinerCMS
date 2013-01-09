<?php
if (!defined ('in')) exit ();
$tohead[] = '<script src="admin/remote/js/gallery.js" type="text/javascript"></script>';
$tohead[] = '<link rel="stylesheet" href="admin/remote/css/gallery.css" type="text/css" />';
$mod = (isset ($_GET['mod'])) ? $_GET['mod'] : 'home';
$out .= '<p class="right">';
if ($_USER_INFO['articles']) $out .= '
 <a href="admin.php?what=articles">' . TempIcon ('cat') . ' ' . $translate['articles'] . '</a>'; $out .='
 <a href="admin.php?what=gallery">' . TempIcon ('cat') . ' ' . $translate['albums'] . '</a>
 <a href="admin.php?what=polls">' . TempIcon ('cat') . ' ' . $translate['polls'] . '</a>
 <a href="admin.php?what=gallery&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['albums.add'] . '</a>
</p>'.n;
switch ($mod) {





/*----- Úvodná tabuľka -----*/

case 'home':
	$out = HeadIfPost ($translate['albums']);
	$sql = @mysql_query("SELECT `id`, `nadpis`, `skr`, `showing` FROM `{$prefix}_gall` WHERE 1=1".FILTER." ORDER BY {$_CONFIG['order']} LIMIT ".(($pag-1)*$limit).",$limit");
	while ($tab = @mysql_fetch_assoc ($sql)) {
		$i = 0;
		$out .= '<div class="album">
	<h3>' . $tab['nadpis'] .'</h3>
	<div class="album_icons">
		<a href="?what=gallery&mod=edit&id=' . $tab['id'] . '">'.TempIcon('edit').'</a>
		<a href="?what=gallery&mod=drop&id=' . $tab['id'] . '">'.TempIcon('delete').'</a>
		<a href="?what=gallery&mod=manage&id=' . $tab['id'] . '">'.TempIcon('cat').'</a>
		<a href="' . rwl ('galeria', $tab['id'] . '-' . $tab['skr']) . '" target="_blank">'.TempIcon('blank').'</a>
	</div>
	<div class="cleaner">&nbsp;</div>
	<a href="?what=gallery&mod=manage&id=' . $tab['id'] . '">';
		$query = mysql_query ("SELECT `id`, CONCAT(`id`, '.', `type`) as `fname`, `type` FROM `{$prefix}_img` WHERE `cat` = {$tab['id']} ORDER BY `added` DESC LIMIT 3");
		while ($data = @mysql_fetch_assoc ($query)) $out .= '		<div class="img' . ++$i . '"><img src="' . _SiteLink . 'media/resampler.php?i=' . $data['id'] . '&amp;t=' . $data['type'] . '&amp;h=120" alt="" class="album' . (($i > 3) ? ' notdisplay' : '') . '" /></div>' . n;
		$out .= '	</a>
	<div class="cleaner">&nbsp;</div>
</div>'.n; };
	if (@mysql_num_rows ($sql) == 0) $out .= '<p>' . $translate['nocontent'] . '</p>'.n;
	$out .= '</table>' . n;
	$out .= GetPagesList ('gall', 'gallery');
break;





/*--- Pridávanie albumu ---*/

case 'add':
	$out = HeadIfPost ($translate['albums.add']);
	if (isset ($_GET['title'], $_GET['description'])) {
		$showing = isset ($_REQUEST['showing']) ? 1 : 0;
		if (@mysql_query ("INSERT INTO `{$prefix}_gall` VALUES (0, ".USER.", '" . adjust ($_REQUEST['title']) . "', '" . SeoTitle ($_REQUEST['title']) . "', $showing, '" . adjust ($_REQUEST['description']) . "');")) {
			$id = mysql_insert_id ();
			Header ('Location: admin.php?what=gallery&mod=manage&id=' . $id);
		} else $out .= GetIcon ('error', $translate['failureadd']);
	};
	$out .= '<form action="' . _SiteForm . '" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
	<strong>' . $translate['title'] . '</strong>
	<input type="text" name="title" />
	<strong>' . $translate['settings'] . '</strong>
	<input type="checkbox" name="showing" checked="checked" id="sh" /> <label for="sh">' . $translate['albums.public'] . '</label>
	<strong>' . $translate['description'] . '</strong>
	<textarea name="description"></textarea>
	</fieldset>
	<input type="submit" value="' . $translate['save'] . '" />
</form>';
break;





/*--- Editácia albumu ---*/

case 'edit':
	$out = HeadIfPost ($translate['albums.edit']);
	if (isset ($_POST['id']) and $id = adjust ($_POST['id'], true) and $info = @mysql_fetch_row (@mysql_query ("SELECT `nadpis`, `popis`, `showing` FROM `{$prefix}_gall` WHERE `id` = $id".FILTER))) {
		if (isset ($_GET['title'], $_GET['description'])) {
			$showing = isset ($_REQUEST['showing']) ? 1 : 0;
			if (@mysql_query ("UPDATE `{$prefix}_gall` SET `nadpis` = '" . adjust ($_REQUEST['title']) . "', `skr` = '" . SeoTitle ($_REQUEST['title']) . "', `showing` = $showing, `popis` = '" . adjust ($_REQUEST['description']) . "' WHERE `id` = $id LIMIT 1")) {
				$out .= GetIcon ('info', $translate['successact']);
				$info[0] = $_REQUEST['title'];
				$info[1] = $_REQUEST['description'];
				$info[2] = $_REQUEST['showing'];
			} else $out .= GetIcon ('error', $translate['failureact']);
		};
		$out .= '<form action="' . _SiteForm . '" method="post">
		<h2>' . $translate['maininfo'] . '</h2>
		<fieldset>
		<strong>' . $translate['title'] . '</strong>
		<input type="text" name="title" value="' . setQ ($info[0], true) . '" />
		<strong>' . $translate['settings'] . '</strong>
		<input type="checkbox" name="showing" ' . (($info[2]==1)?'checked="checked" ':'') . 'id="sh" /> <label for="sh">' . $translate['albums.public'] . '</label>
		<strong>' . $translate['description'] . '</strong>
		<textarea name="description">' . $info[1] . '</textarea>
		</fieldset>
		<input type="submit" value="' . $translate['save'] . '" />
		<input type="reset" value="' . $translate['reset'] . '" />
	</form>';
	} else Header ('Location: ?what=gallery');
break;





/*--- Mazanie albumu ---*/

case 'drop':
	$out = HeadIfPost ($translate['albums.drop']);
	if (isset ($_POST['id']) and $id = adjust ($_POST['id'], true) and $info = @mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_gall` WHERE `id` = $id".FILTER))) {
		if (isset ($_GET['back'])) Header ('Location: admin.php?what=gallery');
		else if (isset ($_GET['drop'])) {
			$sql = @mysql_query ("SELECT `id`, `type` FROM `{$prefix}_img` WHERE `cat` = ".adjust($_GET['id'], true));
			while ($t = @mysql_fetch_row ($sql)) {
				@unlink ("store/gallery/$t[0].$t[1]");
				@unlink ("store/gsmall/$t[0].$t[1]");
			};
			if (@mysql_query ("DELETE FROM `{$prefix}_gall` WHERE `id` = $id LIMIT 1")
			and @mysql_query ("DELETE FROM `{$prefix}_img` WHERE `cat` = $id LIMIT 1"))
			Header ('Location: admin.php?what=gallery');
			else $out .= GetIcon ('error', $translate['failureact']);
		};
		$out .= '<form action="' . _SiteForm . '" method="post">
	<fieldset>
		<strong>' . $translate['sureact'] . '</strong>
		<input type="checkbox" disabled="disabled" checked="checked" /> ' . $translate['albums.delimgs'] . '
	</fieldset>
	<input type="submit" name="drop" value="' . $translate['yes'] . '" />
	<input type="submit" name="back" value="' . $translate['no'] . '" />
</form>';
	} else Header ('Location: ?what=gallery');
break;





/*--- Práca s médiami ---*/
case 'manage':
	if (!(isset ($_REQUEST['id']) and $id = adjust ($_REQUEST['id'], true) and $info = @mysql_fetch_row (@mysql_query ("SELECT `nadpis` FROM `{$prefix}_gall` WHERE `id` = $id".FILTER))))
	Header ('Location: ?what=gallery');
	$out = HeadIfPost (langrep ('albums.mantitle', $info[0]));



	/*--- Pridávanie položky do galérie ---*/
	if (isset ($_REQUEST['add'], $_FILES['file'])) {
	foreach ($_FILES['file']['name'] as $i => $values) {
	if ($_FILES['file']['error'][$i] == 0) {
		switch ($_FILES['file']['type'][$i]) {
			case 'image/jpeg':
			case 'image/pjpeg':
				$type = 'jpg';
				if (@mysql_query ("INSERT INTO `{$prefix}_img` VALUES (0, 'jpg', $id, '" . adjust ($_POST['title'][$i]) . "', '" . adjust ($_GET['description'][$i]) . "', NOW())")
				and $last = mysql_insert_id()
				and @move_uploaded_file ($_FILES['file']['tmp_name'][$i], "store/gallery/$last.$type"))
				$out .= geticon ('info', langrep ('albums.uploadsucc', $_FILES['file']['name'][$i]));
			break;
			case 'image/png':
				$type = 'png';
				if (@mysql_query ("INSERT INTO `{$prefix}_img` VALUES (0, 'png', $id, '" . adjust ($_POST['title'][$i]) . "', '" . adjust ($_GET['description'][$i]) . "', NOW())")
				and $last = mysql_insert_id()
				and @move_uploaded_file ($_FILES['file']['tmp_name'][$i], "store/gallery/$last.$type"))
				$out .= geticon ('info', langrep ('albums.uploadsucc', $_FILES['file']['name'][$i]));
			break;
			case 'image/gif':
				$type = 'gif';
				if (@mysql_query ("INSERT INTO `{$prefix}_img` VALUES (0, 'gif', $id, '" . adjust ($_POST['title'][$i]) . "', '" . adjust ($_GET['description'][$i]) . "', NOW())")
				and $last = mysql_insert_id()
				and @move_uploaded_file ($_FILES['file']['tmp_name'][$i], "store/gallery/$last.$type"))
				$out .= geticon ('info', langrep ('albums.uploadsucc', $_FILES['file']['name'][$i]));
			break;
			case 'audio/mp3':
			case 'audio/mpeg3':
			case 'audio/x-mpeg-3':
			case 'audio/mpeg':
				$type = 'mp3';
				if (@mysql_query ("INSERT INTO `{$prefix}_img` VALUES (0, '$type', $id, '" . adjust ($_POST['title'][$i]) . "', '" . adjust ($_GET['description'][$i]) . "', NOW())")
				and $last = mysql_insert_id()
				and @move_uploaded_file ($_FILES['file']['tmp_name'][$i], "store/gallery/$last.$type"))
				$out .= geticon ('info', langrep ('albums.uploadsucc', $_FILES['file']['name'][$i]));
			break;
			case 'video/x-flv':
			case 'video/flv':
				$type = 'flv';
				if (@mysql_query ("INSERT INTO `{$prefix}_img` VALUES (0, '$type', $id, '" . adjust ($_POST['title'][$i]) . "', '" . adjust ($_GET['description'][$i]) . "', NOW())")
				and $last = mysql_insert_id()
				and @move_uploaded_file ($_FILES['file']['tmp_name'][$i], "store/gallery/$last.$type"))
				$out .= geticon ('info', langrep ('albums.uploadsucc', $_FILES['file']['name'][$i]));
			break;
			case '': $out .= getIcon ('error', $translate['albums.emptyupload']); break;
			default: $out .= getIcon ('error', langrep ('albums.uploadfail', $_FILES['file']['name'][$i])); break;
		};};};



	/*--- Editácia a mazanie obrázku ---*/
	} else if (isset ($_GET['edit']) and !empty ($_GET['edit']) and $img = adjust ($_GET['edit'], true)) {
		if (isset ($_GET['drop'])) {
			list ($type) = @mysql_fetch_row (@mysql_query ("SELECT `type` FROM `{$prefix}_img` WHERE `id` = $img"));
			$flname = $img . '.' . $type;
			@unlink ('store/gallery/' . $flname);
			if (@mysql_query ("DELETE FROM `{$prefix}_img` WHERE `id` = $img LIMIT 1"))
			$out .= geticon ('info', $translate['successact']);
			else $out .= geticon ('error', $translate['failureact']);
		} else {
			if (@mysql_query ("UPDATE `{$prefix}_img` SET `nadpis` = '" . adjust ($_GET['title']) . "', `popis` = '" . adjust ($_GET['description']) . "' WHERE `id` = $img LIMIT 1"))
			$out .= geticon ('info', $translate['successact']);
			else $out .= geticon ('error', $translate['failureact']);
		};
	};



	/*--- Zobrazovanie a pridávanie položiek (form) ---*/
	$out .= '<div id="addMedium">
	<p><a href="#" onclick="return addMedium()">' . TempIcon ('add') . ' ' . $translate['albums.upload'] . '</a></p>
</div>
<div id="hideForm">
<form action="' . str_replace ('&add', '', _SiteForm) . '&amp;add" method="post" enctype="multipart/form-data" id="addMF">
<div id="addFM"><input type="text" name="title[]" /><textarea name="description[]"></textarea><input type="file" name="file[]" /></div>
<p><a href="#" onclick="return addFMp()">' . TempIcon ('add') . ' ' . $translate['albums.more'] . '</a></p>
<input type="submit" value="Pridať" /> <input type="button" value="' . $translate['manual'] . '" onclick="alert(\'' . langrep ('albums.manual', 'jpg / png / gif / mp3 / flv') . '\')" />
</form><br /><br />
</div>';

	$limit = 15;
	list ($count) = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM `{$prefix}_img` WHERE `cat` = $id"));
	$page = (isset ($_GET['page']) and ($_GET['page'] - 1) * $limit < $count and $_GET['page'] > 0) ? $_GET['page'] : ceil ($count / $limit);
	$select = ($page - 1) * $limit;
	
	if ($count > $limit) {
		$out .= '<p align="center">' . "\n";
		for ($i = 1; $i <= ceil ($count / $limit); ++$i)
		$out .= '<a href="?what=gallery&mod=manage&id=' . $id . '&page=' . $i . '">' . (($i == $page) ? "<strong>$i</strong>" : $i) . '</a>' . "\n";
		$out .= '</p>' . "\n";
	};
	
	$CAT_SELECT = '<select name="album">' . "\n";
	$sql = @mysql_query ("SELECT `id`, `nadpis` FROM `{$prefix}_gall` ORDER BY `nadpis` ASC");
	while ($info = @mysql_fetch_assoc ($sql))
	$CAT_SELECT .= "\t\t" . '<option value="' . $info['id'] . '"' . (($info['id'] == $id) ? ' selected="selected"' : '') . '>' . $info['nadpis'] . '</option>' . "\n";
	$CAT_SELECT .= "\t</select>";
	
	$sql = @mysql_query ("SELECT `id`, `nadpis`, `popis`, `type` FROM `{$prefix}_img` WHERE `cat` = $id LIMIT $select, $limit");
	$ii = 0;
	$out .= '<table width="100%" id="editGallery" cellspacing="5px"><tr>';
	while ($info = @mysql_fetch_assoc ($sql)) {
		if ($info['type'] == 'flv') $image = 'media/video.png';
		else if ($info['type'] == 'mp3') $image = 'media/music.png';
		else $image = 'media/resampler.php?i=' . $info['id'] . '&amp;t=' . $info['type'] . '&amp;h=160';
		$nadpis = $info['nadpis'] == '' ? $translate['albums.wtitle'] : $info['nadpis'];
		$out .= '<td><h3>[' . $info['id'] . '] ' . $nadpis . '</h3>
	<img src="' . $image . '" alt="' . setQ ($info['popis']) . '" class="Image" /><br />
	<a href="#" onclick="return editImage('.$ii.', '.$info['id'].', '.$id.', '.$page.', \''.str_replace('&', '&amp;', setQ($info['nadpis'])).'\', \''.str_replace('&', '&amp;', setQ($info['popis'])).'\')">' . TempIcon ('edit') . ' ' . $translate['edit'] . '</a> &nbsp; &nbsp; &nbsp; <a href="?what=gallery&amp;mod=manage&amp;id=' . $id . '&amp;edit=' . $info['id'] . '&amp;drop">' . TempIcon ('delete') . ' ' . $translate['drop'] . '</a>
	<div class="em"></div>
</td>'.n;
	if (++$ii % 3 == 0 and $ii <= 15) $out .= '</tr><tr>'.n;
	};
	$out .= '</tr></table>'.n;
break;
};?>