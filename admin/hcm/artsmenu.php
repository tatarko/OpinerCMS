<?php

$sql = "SELECT seo,nadpis,id FROM {$prefix}_clanky WHERE showing = 1 AND added <= NOW() AND `confirmed` = 1[w] ORDER BY `added` DESC LIMIT [l]";
if (isset ($param2)) $param2 = str_replace ('ciarka', ',', $param2);
if (isset ($param2) and $param2 != '') $sql = str_replace ('[l]', $param2, $sql);
	else $sql = str_replace ('[l]', $_CONFIG['menu_last_arts'], $sql);
if (isset ($param1) and $param1 != '') $sql = str_replace ('[w]', " AND (cat = '$param1' OR cat2 = '$param1' OR cat3 = '$param1')", $sql);
	else $sql = str_replace('[w]', '', $sql);
$query = mysql_query($sql);
if (mysql_num_rows($query) != 0) {
	@$out = $_TEMP['list-start'].n;
	while ($tab = mysql_fetch_row($query)) {
		if (@++$i == 1 and isset ($_TEMP['link-first'])) @$out .= $_TEMP['link-first'];
		else @$out .= $_TEMP['link-start'];
		@$out .= '<a href="' . rwl ('clanok', $tab[2].'-'.$tab[0]) . '">' . $tab[1] . '</a>' . $_TEMP['link-end'].n;
	};
	@$out .= $_TEMP['list-end'].n;
} else $out = '';
?>