<?php
$sql = "SELECT href, title, target, name FROM {$prefix}_links WHERE `location` = 0 ORDER BY `position` ASC";
$query = mysql_query($sql);
if (mysql_num_rows($query) != 0) {
	$out = $_TEMP['list-start'] . n;
	while ($tab = mysql_fetch_row($query)) {
		if(@++$i == 1 and isset ($_TEMP['link-first'])) $out .= $_TEMP['link-first'];
		else $out .= $_TEMP['link-start'];
		$out .= '<a href="' . $tab[0] . '"';
		$out .= ($tab[1] == '') ? '' : ' title="' . $tab[1] . '"';
		$out .= ' target="' . $tab[2] . '">' . $tab[3] . '</a>' . $_TEMP['link-end'] . n;
	};
	$out .= $_TEMP['list-end'].n;
} else $out = '';
?>