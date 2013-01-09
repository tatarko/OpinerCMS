<?php
if (!isset ($param1) or $param = '') $param1 = 'nadpis ASC';
$sql = @mysql_query("SELECT * FROM {$prefix}_cats WHERE showing = 1 ORDER BY $param1");
if (@mysql_num_rows ($sql) != 0) {
	@$out = $_TEMP['list-start'].n;
	while ($tab = @mysql_fetch_row ($sql)) {
		$kolko = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_clanky WHERE showing = 1 AND added <= NOW() AND `confirmed` = 1 AND (cat = '$tab[0]' OR cat2 = '$tab[0]' OR cat3 = '$tab[0]')"));
		if (@++$i == 1 and isset ($_TEMP['link-first'])) $out .= $_TEMP['link-first'];
		else $out .= isset ($_TEMP['link-start']) ? $_TEMP['link-start'] : '<li>';
		$out .= '<a href="' . rwl ('kategoria', $tab[0].'-'.$tab[3]) . '">' . $tab[1] . '</a> <em>(' . $kolko[0] . ')</em>' . ((isset($_TEMP['link-end']))?$_TEMP['link-end']:'</li>') . n;
	};
	@$out .= $_TEMP['list-end'].n;
} else $out = '';
?>