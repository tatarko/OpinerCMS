<?php
if (isset($param1) and $param1 != '' and false !== ($info = @mysql_fetch_row (@mysql_query ("SELECT `file`, `size` FROM `{$prefix}_download` WHERE `id` = '$param1' LIMIT 1"))) and list ($name, $size) = $info) {
	$name = substr ($name, strrpos ($name, '/') + 1);
	if($size > 1024000) $size = round ($size / 1024000, 1) . ' MB';
	else if ($size > 1024) $size = round ($size / 1024, 1) . ' kB';
	else $size .= ' B';
	$out = '<a href="media/download.php?file=' . $param1 . '" title="' . $name . ', ' . $size . '">' . $name . '</a>';
} else $out = '';
?>