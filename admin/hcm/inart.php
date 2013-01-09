<?php
$out = '';
if (!isset ($param1) or empty ($param1)) break;
$param1 = array_filter (explode ('#', $param1));
if (empty ($param1)) break;
foreach ($param1 as $i => $v) $ids[$i] = '`id` = ' . adjust ($v, true);
$ids = implode (' OR ', $ids);
if (false === ($sql = @mysql_query ("SELECT `id`, `type`, `nadpis`, `popis` FROM `{$prefix}_img` WHERE $ids"))
or false === ($browser = loadImageBrowser ('inart')))
return '';

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
	else $out .= ' ' . $browser::call ($tab[0], $tab[1], 'store/gallery/' . implode ('.', array ($tab[0], $tab[1])), $title).n;
	$out .= '  <img src="' . $image . '" alt="' . $title . '" ' . ((MOBILE) ? 'class="imagePreview" ' : '') . '/>' .n. ' </a>' . n;
};
?>