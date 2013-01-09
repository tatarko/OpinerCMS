<?php
if (!defined ('in')) exit ();
$out .= '<p class="right">
 <a href="?what=file-manager">' . TempIcon ('cat') . ' ' . $translate['fman'] . '</a>
 <a href="?what=file-manager&mod=download-manager">' . TempIcon ('cat') . ' ' . $translate['fman.down'] . '</a>
 <a href="?what=statistics">' . TempIcon ('cat') . ' ' . $translate['stats'] . '</a>
 <a href="?what=backup">' . TempIcon ('cat') . ' ' . $translate['backup'] . '</a>
 <a href="?what=labs">' . TempIcon ('cat') . ' ' . $translate['labsprojects'] . '</a>
</p>'.n;
$mainame = 'file-manager';
if (!isset ($_GET['mod'])) $_GET['mod'] = 'home';
switch ($_GET['mod']) {





/*----- Editovanie Súbora -----*/

case 'edit':
	$out = HeadIfPost ($translate['fman.editfile']);
	include ('admin/opiner-text/OpinerText.php');
	if (isset ($_REQUEST['file'], $_SESSION['fmdir']) and $dir = $_SESSION['fmdir'] and $opendir = '.' . $dir . (($dir == '/') ? '' : '/') and @file_exists ($opendir . $_REQUEST['file'])
	and $editor = new OpinerText ($opendir . $_REQUEST['file'], "admin.php?what=$mainame&mod=edit&file={$_REQUEST['file']}")) {
		if (isset ($_GET['ok'], $_GET['text'])) {
			switch ($_GET['ok']) {
				case 'exit': Header ('Location: ?what=' . $mainame); break;
				case 'save-as':
					if (isset ($_REQUEST['new-file-name']) and $_REQUEST['new-file-name'] != '') {
						if (!@file_exists ($opendir . $_REQUEST['new-file-name'])) {
							$_REQUEST['file'] = $_REQUEST['new-file-name'];
							if ($editor = new OpinerText ($opendir . $_REQUEST['new-file-name'], "admin.php?what=$mainame&mod=edit&file={$_REQUEST['file']}", true)) {
								$editor->SetText ($_GET['text']);
								if ($editor->Save ()) $out .= GetIcon ('info', $translate['successact']);
								else $out .= GetIcon ('error', $translate['failureact']);
							} else $out .= GetIcon ('error', $translate['fman.er1']);
						} else $out .= GetIcon ('warning', langrep ('fman.er2', $_REQUEST['new-file-name']));
					} else $out .= GetIcon ('warning', $translate['fman.er3']);
				break;
				case 'reload':break;
				default:
					$editor->SetText ($_GET['text']);
					if ($editor->Save ()) $out .= GetIcon ('info', $translate['successact']);
					else $out .= GetIcon ('error', $translate['failureact']);
				break;
			};
		};
		$tohead[] = '<script src="admin/opiner-text/OpinerText.js" type="text/javascript"></script>';
		$tohead[] = '<link rel="stylesheet" href="admin/opiner-text/style.css" type="text/css" />';
		$out .= $editor;
		$out .= '</form>';
	} else Header ('Location: ?what=' . $mainame);
break;





/*----- Premenovanie Súbora -----*/

case 'rename':
	$out = HeadIfPost ($translate['fman.rename']);
	if (isset ($_REQUEST['file'], $_SESSION['fmdir']) and $dir = $_SESSION['fmdir']
	and $opendir = '.' . $dir . (($dir == '/') ? '' : '/') and @file_exists ($opendir . $_REQUEST['file'])) {
		if (isset ($_REQUEST['newfilename'])) {
			if ($_REQUEST['newfilename'] == $_REQUEST['file'] or (
			!@file_exists ($opendir . $_REQUEST['newfilename']) and rename ($opendir . $_REQUEST['file'], $opendir . $_REQUEST['newfilename']))
			) Header ('Location: admin.php?what=' . $mainame . '&mes=info-action-ok');
			else $out .= GetIcon ('error', $translate['failureact']);
		};
		$out .= '<form action="admin.php?' . $_SERVER['QUERY_STRING'] . '" method="post">
	<input type="text" name="newfilename" value="' . $_REQUEST['file'] . '" />
	<input type="submit" value="' . $translate['fman.rename'] . '" />
</form>';
	};
break;





/*----- Mazanie súboru -----*/

case 'delete':
	$out = HeadIfPost ($translate['fman.dropfile']);
	if (isset ($_REQUEST['file'], $_SESSION['fmdir']) and $dir = $_SESSION['fmdir']
	and $opendir = '.' . $dir . (($dir == '/') ? '' : '/')) {
		if (isset ($_REQUEST['ok'])) {
			if ($_REQUEST['ok'] == $translate['yes']) {
				@unlink ($opendir . $_REQUEST['file']);
				Header ('Location: admin.php?what=' . $mainame . '&mes=info-action-ok');
			} else Header ('Location: admin.php?what=' . $mainame);
		};
		$out .= '<form action="admin.php?' . $_SERVER['QUERY_STRING'] . '" method="post">
	<strong>' . $translate['sureact'] . '</strong><br />
	<input type="submit" value="' . $translate['yes'] . '" name="ok" />
	<input type="submit" value="' . $translate['no'] . '" name="ok" />
</form>';
	};
break;





/*----- Vytvoriť Súbor -----*/

case 'create':
	$out = HeadIfPost ($translate['fman.createfile']);
	if (isset ($_GET['ok'])) {
		if ($_REQUEST['ok'] == 'Vytvoriť') {
			if (isset ($_REQUEST['filename'], $_SESSION['fmdir']) and $filename = $_REQUEST['filename'] and $dirname = $_SESSION['fmdir']) {
				$sep = ($dirname == '/') ? '' : '/';
				if (!file_exists (substr ($dirname . $sep . $filename, 1))) {
					if (false !== ($file = @fopen ('.' . $dirname . $sep . $filename, 'w')) and fclose ($file))
					Header ('Location: ?what=' . $mainame . '&mod=edit&file=' . $filename);
					else $out .= GetIcon ('error', 'Nie je možné vytvoriť súbor <kbd>'.$filename.'</kbd>');
				} else $out .= GetIcon ('warning', "Súbor <kbd>$filename</kbd> už existuje");
			};
		} else Header ('Location: ?what=' . $mainame);
	};
	if (!isset ($_REQUEST['filename'])) $_REQUEST['filename'] = $translate['fman.nfile'] . '.txt';
	$out .= '<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post">
	<input type="text" name="filename" size="35" value="'.$_REQUEST['filename'].'" />
	<input type="submit" name="ok" value="' . $translate['create'] . '" />
	<input type="submit" name="ok" value="' . $translate['back'] . '" />
</form>'.n;
break;





/*----- Nahrať Súbor -----*/

case 'upload':
	$out = HeadIfPost ($translate['fman.upload']);
	if (isset ($_POST['ok'])) {
		if (isset ($_SESSION['fmdir']) and $dir = $_SESSION['fmdir'] and $opendir = '.' . $dir . (($dir == '/') ? '' : '/')) {
			if (isset ($_POST['rewrite'])) {
				if (isset ($_FILES['file']) and $_FILES['file']['error'] == '0') {
					if (@move_uploaded_file ($_FILES['file']['tmp_name'], $opendir . $_FILES['file']['name']))
					$out .= getIcon ('info', $translate['fman.succupl']);
					else $out .= getIcon ('warning', $translate['fman.er4']);
				} else $out .= getIcon ('error', $translate['fman.er5']);
			} else {
				if (isset ($_FILES['file']) and !@file_exists ($opendir . $_FILES['file']['name'])) {
					if (isset ($_FILES['file']) and $_FILES['file']['error'] == '0') {
						if (@move_uploaded_file ($_FILES['file']['tmp_name'], $opendir . $_FILES['file']['name']))
						$out .= getIcon ('info', $translate['fman.succupl']);
						else $out .= getIcon ('error', $translate['fman.er4']);
					} else $out .= getIcon ('error', $translate['fman.er5']);
				} else $out .= getIcon ('error', $translate['fman.er6']);
			};
		} else Header ('Location: ?what=' . $mainame);
	};
	$out .= '<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post" enctype="multipart/form-data">
	<input type="file" name="file" size="35" /><br />
	<input type="checkbox" name="rewrite" id="rew" /> <label for="rew">' . $translate['fman.rw'] . '</label><br />
	<input type="submit" name="ok" value="' . $translate['fman.upl'] . '" />'.n;
	$out .= '</form>'.n;
break;





/*----- Vytvoriť Priečinok -----*/

case 'create-folder':
	$out = HeadIfPost ($translate['fman.createfolder']);
	if (isset ($_GET['ok'])) {
		if ($_REQUEST['ok'] == $translate['create']) {
			if (isset ($_REQUEST['dirname'], $_SESSION['fmdir']) and $dirn = $_REQUEST['dirname'] and $dirname = $_SESSION['fmdir']) {
				$sep = ($dirname == '/') ? '' : '/';
				if (!@is_dir (substr ($dirname . $sep . $dirn, 1))) {
					if (mkdir ('.' . $dirname . $sep . $dirn, 0777))
					Header ('Location: ?what=' . $mainame . '&dir=' . $dirname . $sep . $dirn . '&mes=info-action-ok');
					else $out .= GetIcon ('error', langrep ('fman.er7', $dirn));
				} else $out .= GetIcon ('warning', langrep ('fman.er8'. $dirname));
			};
		} else Header ('Location: ?what=' . $mainame);
	};
	if (!isset ($_REQUEST['dirname'])) $_REQUEST['dirname'] = $translate['fman.ndir'];
	$out .= '<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post">
	<input type="text" name="dirname" size="35" value="'.$_REQUEST['dirname'].'" />
	<input type="submit" name="ok" value="' . $translate['create'] . '" />
	<input type="submit" name="ok" value="' . $translate['back'] . '" />
</form>'.n;
break;





/*----- Mazanie priečinku -----*/

case 'deldir':
	$out = HeadIfPost ($translate['fman.dropfolder']);
	if (isset ($_REQUEST['file'], $_SESSION['fmdir']) and $dir = $_SESSION['fmdir']
	and $opendir = '.' . $dir . (($dir == '/') ? '' : '/')) {
		if (isset ($_REQUEST['ok'])) {
			if ($_REQUEST['ok'] == $translate['yes']) {
				if (@rmdir ($opendir . $_REQUEST['file']))
				Header ('Location: admin.php?what=' . $mainame . '&mes=info-action-ok');
				else $out .= GetIcon ('errror', $translate['failureact']);
			} else Header ('Location: admin.php?what=' . $mainame);
		};
		$out .= '<form action="admin.php?' . $_SERVER['QUERY_STRING'] . '" method="post">
	<strong>' . $translate['sureact'] . '</strong><br />
	<input type="submit" value="' . $translate['yes'] . '" name="ok" />
	<input type="submit" value="' . $translate['no'] . '" name="ok" />
</form>';};
break;





/*----- Premenovanie Priečinku -----*/

case 'rendir':
	$out = HeadIfPost ($translate['fman.renamefolder']);
	if (isset ($_REQUEST['file'], $_SESSION['fmdir']) and $dir = $_SESSION['fmdir']
	and $opendir = '.' . $dir . (($dir == '/') ? '' : '/') and @is_dir ($opendir . $_REQUEST['file'])) {
		if (isset ($_REQUEST['newfilename'])) {
			if ($_REQUEST['newfilename'] == $_REQUEST['file'] or (
			!@is_dir ($opendir . $_REQUEST['newfilename']) and rename ($opendir . $_REQUEST['file'], $opendir . $_REQUEST['newfilename']))
			) Header ('Location: admin.php?what=' . $mainame . '&mes=info-action-ok');
			else $out .= GetIcon ('error', $translate['failureact']);
		};
		$out .= '<form action="admin.php?' . $_SERVER['QUERY_STRING'] . '" method="post">
	<input type="text" name="newfilename" value="' . $_REQUEST['file'] . '" />
	<input type="submit" value="' . $translate['fman.renamefolder'] . '" />
</form>';};
break;





/*----- Sledovanie Sťahovania Súborov -----*/

case 'download-manager':
	$out = HeadIfPost ($translate['fman.down']);
	$out .= GetPagesList ('download', $mainame . '&mod=download-manager', '', 'file');
	$out .= '<table id="admintable" cellspacing="3px">
	<tr><th width="30px">ID</th><th>' . $translate['fman.direction'] . '</th><th width="70px">' . $translate['fman.downloaded'] . '</th><th width="50px">' . $translate['action'] . '</th></tr>' . n;
	$sql = @mysql_query("SELECT `file`, `id`, `hits` FROM `{$prefix}_download` WHERE 1 = 1 " . GetFilterQ (true, 'file') . " ORDER BY `id` DESC LIMIT " . (($pag - 1) * $limit) . ", $limit");
	if (@mysql_num_rows ($sql) > 0) {
		while ($tab = @mysql_fetch_row ($sql)){
			$out .= '<tr><td>' . $tab[1] . '</td><td>' . $tab[0] . '</td>';
			$out .= '<td>' . oddel ($tab[2]) . 'x</td><td>';
			$out .= '<a href="javascript: alert(\'' . $translate['fman.hcm'] . ':\n[hcm]down, ' . $tab[1] . '[/hcm]\');" title="' . $translate['fman.hcm'] . '">' . TempIcon ('blank') . '</a>';
			$out .= '<a href="admin.php?what=' . $mainame . '&mod=drop-downlog&id=' . $tab[1] . '" title="' . $translate['fman.dropdown'] . '">' . TempIcon ('delete') . '</a>';
			$out .= '</td></tr>';
		};
	} else $out .= '<tr><td colspan="4">' . $translate['nocontent'] . '</td></tr>' . n;
	$out .= '</table>';
break;





/*----- Odstránenie z počítania stiahnutí -----*/

case 'drop-downlog':
	if (!empty ($_GET['id']) and @mysql_query("DELETE FROM `{$prefix}_download` WHERE `id` = " . adjust ($_GET['id'], true) . " LIMIT 1"))
	Header ('Location: admin.php?what=' . $mainame . '&mod=download-manager&mes=info-action-ok');
	else Header ('Location: admin.php?what=' . $mainame . '&mod=download-manager&mes=error-action-er');
break;





/*----- Odstránenie z počítania stiahnutí -----*/

case 'add-downlog':
	if (isset ($_REQUEST['file'], $_SESSION['fmdir']) and $dir = $_SESSION['fmdir']
	and $opendir = '.' . $dir . (($dir == '/') ? '' : '/') and $file = $_REQUEST['file'] and @file_exists ($opendir . $file)
	and !@mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_download` WHERE `file` = '$opendir{$file}' LIMIT 1"))
	and $fl = filesize  ($opendir . $file) and @mysql_query ("INSERT INTO `{$prefix}_download` VALUES (0, 0, '$opendir{$file}', $fl);"))
	Header ('Location: admin.php?what=' . $mainame . '&mod=download-manager&mes=info-action-ok');
	else Header ('Location: admin.php?what=' . $mainame . '&mod=download-manager&mes=error-action-er');
break;





/*----- Úvodná Tabuľka -----*/

default:
	clearstatcache();
	if (isset ($_REQUEST['mes']) and $_REQUEST['mes'] == 'info-action-ok') $out .= GetIcon ('info', $translate['successact']);
	else if (isset ($_REQUEST['mes']) and $_REQUEST['mes'] == 'error-action-er') $out .= GetIcon ('error', $translate['failureact']);
	$out = HeadIfPost ($translate['fman'], 'file-manager');
	if (isset ($_REQUEST['dir']) and is_dir ('.' . $_REQUEST['dir'])) $dir = $_REQUEST['dir'];
	else if (isset ($_SESSION['fmdir']) and is_dir ('.' . $_SESSION['fmdir'])) $dir = $_SESSION['fmdir'];
	else $dir = '/store/files';
	$where = str_replace ('admin.php', '', $_SERVER['PHP_SELF']);
	$where = substr ($_SERVER['SERVER_NAME'] . $where, 0, -1);
	if (substr ($dir, 0, 1) == '/') $dir = substr ($dir, 1);
	if (substr ($dir, -1, 1) == '/') $dir = substr ($dir, 0, -1);
	if (!@is_dir ('./' . $dir)) {
		$out .= GetIcon ('error', langrep ('fman.er9', $dir));
		$dir = 'store/files';
	};
	$_SESSION['fmdir'] = '/' . $dir;



	/*--- Horná Lišta ---*/

	$uplink = '/' . $dir;
	if ($uplink != '/') {
		$last = strrpos ($uplink, '/');
		$uplink = substr ($uplink, 0, $last);
		if ($uplink == '') $uplink = '/';
		$_LISTA[] = '<a href="admin.php?what=' . $mainame . '&dir=' . $uplink . '"><img src="admin/opiner-text/images/icons/folder-up.png" class="lista_icon" />' . $translate['fman.folderup'] . '</a>';
	};
	$_LISTA[] = '<a href="admin.php?what=' . $mainame . '&mod=create"><img src="admin/images/icon-add.png" class="lista_icon" />' . $translate['fman.createfile'] . '</a>';
	$_LISTA[] = '<a href="admin.php?what=' . $mainame . '&mod=upload"><img src="admin/opiner-text/images/icons/upload.png" class="lista_icon" />' . $translate['fman.upload'] . '</a>';
	$_LISTA[] = '<a href="admin.php?what=' . $mainame . '&mod=create-folder"><img src="admin/opiner-text/images/icons/create-folder.png" class="lista_icon" />' . $translate['fman.createfolder'] . '</a>';
	$_LISTA[] = '<a href="admin.php?what=' . $mainame . '&dir=/store/files"><img src="admin/opiner-text/images/icons/go-home.png" class="lista_icon" />' . $translate['fman.homefolder'] . '</a>';
	if ($dir != '') {
		$diring = array ();
		for ($direx = '/' . $dir; false !== ($pos = strrpos ($direx, '/')); ) {
			$linkerdir = substr ($direx, 0, $pos);
			$linkerdir = ($linkerdir == '') ? '/' : $linkerdir;
			$diring[] = array ($direx, substr ($direx, $pos + 1));
			$direx = substr ($direx, 0, $pos);
		};
		krsort ($diring);
		foreach ($diring as $value) $_TO[] = '<a href="admin.php?what=' . $mainame . '&dir=' . $value[0] . '">' . $value[1] . '</a>';
		if (isset ($_TO)) {
			$pre1 = explode ('/', $where);
			$pre2 = count ($pre1) - 1;
			$pre3 = $pre1[$pre2];
			$_LISTA[] = '<a href="admin.php?what=' . $mainame . '&dir=/">' . $pre3 . '</a>/' . implode ('/', $_TO);
		};
	};

	$lista = implode ("\n<span class=\"sep\">|</span>\n", $_LISTA);
	$out .= '<div id="fileman_lista">'.n;
	$out .= $lista;
	$out .= '</div>'.n;



	/*--- Výpis Adresára ---*/

	$FM_DIRS = array ();
	$FM_FILES = array ();
	$out .= '<table id="filemanager" align="center" cellspacing="7px">'.n.'	<tr>'.n;
	$int = 0;
	$opendir = opendir ('./' . $dir);
	while ($file = readdir ($opendir)) {
		if ($file != '.' and $file != '..') {
			if (is_dir ('./' . $dir . '/' . $file)) $FM_DIRS[] = $file;
			else $FM_FILES[] = $file;
		};
	};
	closedir ($opendir);
	asort ($FM_DIRS);
	asort ($FM_FILES);
	$sep = ($dir == '') ? '' : '/';
	foreach ($FM_DIRS as $dirname) {
		$out .= '		<td><center><a href="admin.php?what=' . $mainame . '&dir=/' . $dir . $sep . $dirname . '" title="' . $translate['fman.folder'] . ' \'' . $dirname . '\'">' . FileManagerIcon ('folder') . '<br />' . ((strlen ($dirname) > 15) ? substr ($dirname, 0, 10) . '...' : $dirname) . '</a></center><div class="actions">';
		$out .= '<a href="?what='.$mainame.'&mod=rendir&file=' . $dirname . '" title="' . $translate['fman.renamefolder'] . '">'.TempIcon('blank').'</a>';
		$out .= '<a href="?what='.$mainame.'&mod=deldir&file=' . $dirname . '" title="' . $translate['fman.dropfolder'] . '">'.TempIcon('delete').'</a>';
		$out .= '</div></td>'.n;
		if (++$int % 7 == 0) $out .= '	</tr>'.n.'	<tr>'.n;
	};
	foreach ($FM_FILES as $filename) {
		$filelinker = './' . $dir . $sep . $filename;
		if (false !== ($dot = strrpos ($filename, '.')))
			$filetype = strtolower (substr ($filename, $dot + 1));
			else $filetype = 'blank';
		if (false === ($icon = FileManagerIcon ($filetype, $filelinker))) {
			if (is_writable ($filelinker)) $icon = 'admin/opiner-text/images/mime/text.png';
			else if (is_executable ($filelinker)) $icon = 'admin/opiner-text/images/mime/exe.png';
			else $icon = 'admin/opiner-text/images/mime/blank-file.png';
			$icon = '<img src="' . $icon . '" style="border:0;max-width:96px;max-height:96px;" alt="" />';
		};
		$out .= '		<td><center><a href="http://' . $where . '/' . $dir . $sep . $filename . '" title="' . $translate['fman.file'] . ' \'' . $filename . '\'" target="_blank">' . $icon . '<br />' . ((strlen ($filename) > 15) ? substr ($filename, 0, 10) . '...' : $filename) . '</a></center><div class="actions">';
		if (FileManagerCanOpen ($filetype)) $out .= '<a href="?what='.$mainame.'&mod=edit&file=' . $filename . '" title="' . $translate['fman.editfile'] . '">'.TempIcon('edit').'</a>';
		$out .= '<a href="?what='.$mainame.'&mod=rename&file=' . $filename . '" title="' . $translate['fman.rename'] . '">'.TempIcon('blank').'</a>';
		$out .= '<a href="?what='.$mainame.'&mod=delete&file=' . $filename . '" title="' . $translate['fman.dropfile'] . '">'.TempIcon('delete').'</a>';
		$out .= '<a href="?what='.$mainame.'&mod=add-downlog&file=' . $filename . '" title="' . $translate['fman.add2down'] . '">'.TempIcon('add').'</a>';
		$out .= '</div></td>'.n;
		if (++$int % 7 == 0) $out .= '	</tr>'.n.'	<tr>'.n;};
	$out .= '	</tr>'.n.'</table>'.n;
	$out = str_replace ('	<tr>'.n.'	</tr>'.n, '', $out);
	if (empty ($FM_DIRS) and empty ($FM_FILES)) $out .= getIcon ('warning', $translate['fman.er0']);
	$tohead[] = '<link rel="stylesheet" href="admin/remote/css/fman.css" type="text/css" />';
break;
};
?>