<?php
$query = mysql_query("SELECT href, title, target, name FROM {$prefix}_links WHERE `location` = 2 ORDER BY `position` ASC");
if (mysql_num_rows($query) != 0) {
	$out = '<ul>' . n;
	while ($tab = mysql_fetch_row($query)) {
		$out .= '	<li><a href="' . $tab[0] . '"';
		$out .= ($tab[1] == '') ? '' : ' title="' . $tab[1] . '"';
		$out .= ' target="' . $tab[2] . '">' . $tab[3] . '</a></li>' . n;
	};
	$out .= '</ul>'.n;
} else $out = '';
?>