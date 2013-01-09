<?php
$sql = "SELECT seo,nadpis,id,`reads` FROM {$prefix}_clanky WHERE showing = 1 AND added <= NOW() AND `confirmed` = 1 ORDER BY `reads` DESC LIMIT [l]";
if (isset ($param1) and $param1 != '') $sql = str_replace ('[l]', $param1, $sql);
	else $sql = str_replace ('[l]', $_CONFIG['menu_top_arts'], $sql);
$query = mysql_query($sql);
if (mysql_num_rows($query) != 0) {
	$out = $_TEMP['list-start'] . n;
	while ($tab = mysql_fetch_row($query)) {
		if(@++$i == 1 and isset ($_TEMP['link-first'])) $out .= $_TEMP['link-first'];
		else $out .= $_TEMP['link-start'];
		$out .= '<a href="' . rwl ('clanok', $tab[2].'-'.$tab[0]) . '">' . $tab[1] . '</a> <em>('.$tab[3].'x)</em>' . $_TEMP['link-end'] . n;
	};
	$out .= $_TEMP['list-end'].n;
} else $out = '';
?>