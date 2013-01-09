<?php

$out = '';
if (!isset ($param1) or $param1 == '') break;
if (!isset ($param2) or $param2 == '') $param2 = $_CONFIG['list_imgs'];
$param2 = str_replace ('(ciarka)', ', ', $param2);
$sql = @mysql_query ("SELECT `id`, `type`, `nadpis`, `popis` FROM `{$prefix}_img` WHERE `cat` = '$param1' ORDER BY `id` ASC LIMIT $param2");
$gallId = 'g' . substr (md5 (microtime(true)), 0, 8);

if (mysql_num_rows ($sql) != 0
and false !== ($browser = loadImageBrowser ($gallId))) {
	$out = '<div style="text-align:justify;">'.n;
	while ($tab = mysql_fetch_row ($sql)) {
	        $title = $tab[2] . (($tab[2] != '' and $tab[3] != '') ? ' - ' : '') . $tab[3];
	        if (empty ($title)) $title = $translate['notitle'];

		if ($tab[1] == 'flv')
		$image = 'media/video.png';
		else if ($tab[1] == 'mp3')
		$image = 'media/music.png';
		else $image = MOBILE ? "media/image.php?file=store/gallery/$tab[0].$tab[1]&amp;h=45&amp;w=60" : "media/resampler.php?i=$tab[0]&amp;t=$tab[1]&amp;h=120";

		if (MOBILE and $tab[1] != 'flv' and $tab[1] != 'mp3')
		$out .= ' <a href="media/resampler.php?i=' . $tab[0] . '&amp;t=' . $tab[1] . '&amp;h=480">'.n;
		else $out .= ' ' . $browser -> call ($tab[0], $tab[1], 'store/gallery/' . implode ('.', array ($tab[0], $tab[1])), $title).n;
		$out .= '  <img src="' . $image . '" alt="' . $title . '" ' . ((MOBILE) ? 'class="imagePreview" ' : '') . '/>' .n. ' </a>' . n;
	};
	$out .= '</div>' . n;
} else $out .= "<p>{$translate['nocontent']}</p>";
?>