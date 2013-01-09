<?php
if (!isset ($param1) or $param1 == '') $param1 = $_CONFIG['list_cats'];
$param1 = str_replace ('(ciarka)', ', ', $param1);
$sql = @mysql_query ("SELECT `nadpis`, `skr`, `id`, `popis`, `autor` FROM `{$prefix}_gall` WHERE `showing` = 1 ORDER BY `id` DESC LIMIT $param1");
if (mysql_num_rows ($sql) == 0) {
	$out = "<p>{$translate['nocontent']}</p>";
	break;
};
while ($tab = @mysql_fetch_row($sql)) {
	list ($count) = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM `{$prefix}_img` WHERE `cat` = $tab[2]"));
	@$out .= '<h2><a href="' . rwl ('galeria', $tab[2] . '-' . $tab[1]) . '">' . $tab[0] . '</a></h2>
<p>' . langrep ('albumsx', GetAuthorName ($tab[4]), $count) . '</p>' . n;
	$media = @mysql_query ("SELECT `id`, `type` FROM `{$prefix}_img` WHERE `cat` = '$tab[2]' AND `type` != 'flv' AND `type` != 'mp3' ORDER BY RAND() LIMIT 3");
	while ($medium = mysql_fetch_array ($media))
	$out .= '<a href="' . rwl ('galeria', $tab[2] . '-' . $tab[1]) . '"><img src="' . ((MOBILE)?'media/image.php?file=store/gallery/' . $medium['id'] . '.' . $medium['type'] . '&amp;h=45&amp;w=60':'media/resampler.php?i=' . $medium['id'] . '&amp;t=' . $medium['type'] . '&amp;h=90') . '" alt="" class="imagePreview" /></a>' . n;
	if ($tab[3] != '') $out .= '<p><em>' . $tab[3] . '</em></p>' . n;
	
};
?>