<?php
if (!isset ($param1) or empty ($param1)) break;
$param2 = isset ($param2) and $param2 == 1 ? 1 : 0;
$param3 = (isset ($param3) and !empty ($param3)) ? adjust ($param3, true) : 10;
$param4 = (isset ($param4) and !empty ($param4)) ? adjust ($param4, true) : 512;
$hash = 'store/cache/feed_' . md5 ($param1.$param2.$param3.$param4) . '.php';
if (@file_exists ($hash)) {
	include ($hash);
	if (time () - 900 > $time) @unlink ($hash);
	return $out;
};
$out = '';
if ($xml = @simplexml_load_file ($param1) and isset ($xml->channel->item)) {
	mb_internal_encoding ('UTF-8');
	for ($i = 0; $i < $param3 and isset ($xml->channel->item[$i]); ++$i) {
		$values = $xml->channel->item[$i];
		$array[] = array (
			'link' => $values -> link,
			'title' => $values -> title,
			'description' => ((mb_strlen ($values->description) > $param4) ? mb_substr ($values -> description, 0, mb_strpos ($values->description, ' ', $param4)) . '...' : $values -> description),
		);
	};
} else return "<p>{$translate['er16']}</p>";
if (!isset ($array)) return "<p>{$translate['er17']}</p>";
if ($param2 == 1) {
	foreach ($array as $item) $out .= '<p><strong><a href="' . $item['link'] . '">' . strip_tags ($item['title']) . '</a></strong><br />' . strip_tags ($item['description']) . '</p>'.n;
} else {
	$out .= '<ul>'.n;
	foreach ($array as $item) $out .= '	<li><a href="' . $item['link'] . '">' . strip_tags ($item['title']) . '</a></li>'.n;
	$out .= '</ul>';
};
@file_put_contents ($hash, "<?php\n\$time = " . time () . ";\n\$out = " . var_export ($out, true) . "; ?>");
?>