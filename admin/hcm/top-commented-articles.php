<?php
$limit = (isset ($param1) and !empty ($param1)) ? adjust ($param1, true) : $_CONFIG['menu_top_coms_arts'];
$sql = @mysql_query ("SELECT `a`.`id`, `a`.`nadpis`, `a`.`seo`, COUNT(`b`.`id`) AS `comments` FROM `{$prefix}_clanky` as `a`, `{$prefix}_comments` as `b` WHERE `a`.`showing` = 1 AND `a`.`confirmed` = 1 AND `a`.`added` <= NOW() AND `b`.`kde` = CONCAT('clanok_', `a`.`id`) GROUP BY `a`.`id` ORDER BY `comments` DESC LIMIT $limit");
if (@mysql_num_rows ($sql) == 0) return '';
$out = (isset ($_TEMP['list-start']) ? $_TEMP['list-start'] : '<ul>') . n;
while ($info = mysql_fetch_assoc ($sql)) {
	if (@++$i == 1 and isset ($_TEMP['link-first'])) $out .= $_TEMP['link-first'];
	else if (isset ($_TEMP['link-start'])) $out .= $_TEMP['link-start'];
	else $out .= ' <li>';
	$out .= '<a href="' . rwl ('clanok', $info['id'] . '-' . $info['seo']) . '">' . $info['nadpis'] . '</a> <em>(' . $info['comments'] . 'x)</em>';
	if (isset ($_TEMP['link-end'])) $out .= $_TEMP['link-end'] . n;
	else $out .= '</li>' . n;
};
$out .= (isset ($_TEMP['list-end']) ? $_TEMP['list-end'] : '') . n;
?>