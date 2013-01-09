<?php
$tab1 = "`{$prefix}_clanky`";
$tab2 = "`{$prefix}_iplog`";
$sql = "SELECT $tab1.seo, $tab1.nadpis, $tab1.id,
	ROUND(SUM($tab2.hodnota)/COUNT($tab2.id)) AS hodnotenie
	FROM $tab1, $tab2 WHERE $tab1.showing = 1 AND $tab1.`confirmed` = 1 AND $tab1.added <= NOW() AND $tab2.what = CONCAT('hodnotenie_art_',$tab1.id)
	GROUP BY $tab1.id ORDER BY hodnotenie DESC LIMIT [l]";
if (isset ($param1) and $param1 != '') $sql = str_replace ('[l]', $param1, $sql);
	else $sql = str_replace ('[l]', $_CONFIG['menu_topvoted_arts'], $sql);
$query = mysql_query($sql);
if (mysql_num_rows($query) != 0) {
	$out = $_TEMP['list-start'] . n;
	while ($tab = mysql_fetch_row($query)) {
		if(@++$i == 1 and isset ($_TEMP['link-first'])) $out .= $_TEMP['link-first'];
		else $out .= $_TEMP['link-start'];
		$out .= '<a href="' . rwl ('clanok', $tab[2].'-'.$tab[0]) . '">' . $tab[1] . '</a> <em>(' . $tab[3] . '%)</em>' . $_TEMP['link-end'] . n;
	};
	$out .= $_TEMP['list-end'].n;
} else $out = '';
?>