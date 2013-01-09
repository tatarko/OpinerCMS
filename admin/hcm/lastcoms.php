<?php

$sql = "SELECT kde,autor,id FROM {$prefix}_comments WHERE kde != 'fasttext' ORDER BY id DESC LIMIT [l]";
if (isset ($param1) and $param1 != '') $sql = str_replace ('[l]', $param1, $sql);
	else $sql = str_replace ('[l]', $_CONFIG['menu_coms'], $sql);
$query = mysql_query($sql);
if (mysql_num_rows($query) != 0 and $_CONFIG['global_comments'] == 1) {
	$out = $_TEMP['list-start'] . n;
	while ($tab = mysql_fetch_row($query)) {
		if (substr ($tab[0], 0, 6) == 'clanok') {
			$info = @mysql_fetch_row (@mysql_query ("SELECT seo,id FROM {$prefix}_clanky WHERE id='" . substr ($tab[0], 7) . "'"));
			$link = rwl ('clanok', $info[1].'-'.$info[0]);}
		else if (substr ($tab[0], 0, 3) == 'cat') {
		$info = @mysql_fetch_row (@mysql_query ("SELECT skr,id FROM {$prefix}_gall WHERE id='" . substr ($tab[0], 4) . "'"));
			$link = rwl ('galeria', $info[1].'-'.$info[0]);}
		else if (substr ($tab[0], 0, 3) == 'sec') {
			$info = @mysql_fetch_row (@mysql_query ("SELECT seo,id FROM {$prefix}_sec WHERE id='" . substr ($tab[0], 4) . "'"));
			$link = rwl ('sekcia', $info[1].'-'.$info[0]);}
		else if ($tab[0] == 'kniha') {
			$link = rwl ('kniha', 1);}
		else if (substr ($tab[0], 0, 3) == 'img') {
			$link = 'codes/pviewer/pviewer.php?img=' . substr ($tab[0], 4);};
		if ($tab[1] == 'admin') $tab[1] = $_CONFIG['author'];
		if (@++$i == 1 and isset ($_TEMP['link-first'])) $out .= $_TEMP['link-first'];
			else $out .= $_TEMP['link-start'];
		$out .= '<a href="' . $link . '#comment-' . $tab[2] . '">' . $tab[1] . '</a>' . $_TEMP['link-end'] . n;
	};
	$out .= $_TEMP['list-end'].n;
} else $out = '';

?>