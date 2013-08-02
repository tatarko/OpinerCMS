<?php
if ($_CONFIG['twittername'] != '' and (!isset ($cache['twittercheck']) or $cache['twittercheck'] < time()-$_CONFIG['twittertime']*60)
and $xml = @simplexml_load_file ('http://twitter.com/statuses/user_timeline.xml?screen_name=' . $_CONFIG['twittername'] . '&count=5') and isset ($xml->status)) {
	foreach ($xml -> status as $status) {
		$text = adjust (preg_replace (array ("/@([A-Za-z0-9_]*)/i", "/#([A-Za-z0-9_]*)/i"), array ('"@$1":http://twitter.com/$1', '"#$1":http://twitter.com/#search?q=%23$1'), $status -> text) . " (*\"Twitter\":http://twitter.com/*)");
		$datetime = date ('Y-m-d H:i:s', strtotime ($status -> created_at));
		if (!@mysql_fetch_row (mysql_query ("SELECT `id` FROM `{$prefix}_microblog` WHERE `text` = '" . $text . "' LIMIT 1")))
		@mysql_query ("INSERT INTO `{$prefix}_microblog` VALUES (0, 0, '" . $datetime . "', '" . $text . "');");
	};
	$cache['twittercheck'] = time();
};

$sql = mysql_query ("SELECT DATE_FORMAT(added, '%d.%m.%Y, %H:%i'), text FROM {$prefix}_microblog ORDER BY added DESC LIMIT {$_CONFIG['menu_microblog']}");
$out = '<p>';
if (mysql_num_rows($sql) != 0) {
	while ($tab = mysql_fetch_row($sql)) {
		$out .= '<strong>' . $tab[0] . '</strong>: ';
		$out .= str_replace (array ("\n", '<p>', '</p>', '  '), array (' ', '', '', ' '), OpinerAutoLoader::texyla ($tab[1], 'admin'));
		$out .= '<br /><br />' . n;
	};
} else $out .= $translate['nocontent'] . "<br /><br />\n";
$out .= '<a href="' . rwl ('stranka', 'news') . '" title="'.$_CONFIG['microblog'].'">' . $translate['stats.goto'] . ' ' . $_CONFIG['microblog_head'].'</a></p>';
?>