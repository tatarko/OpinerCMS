<?php

$out = '';
if (!isset ($param1) or $param1 == '') break;
if ($_CONFIG['global_comments'] != 1) break;
if (!isset ($param2) or $param2 == '') $param2 = $_CONFIG['list_coms'];
$param3 = (isset ($param3) and $param3 == '1') ? true : false;
$kolko = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_comments WHERE kde='$param1'"));
if (!$param3) {
	$to = str_replace ('index.php', '', $_SERVER['PHP_SELF']);
	$uri = $_SERVER['REQUEST_URI'];
	$uri = str_replace ('.html', '', $uri);
	$uri = str_replace ('?', '', $uri);
	$uri = str_replace ('=', '-', $uri);
	$uri = str_replace ($to, '', $uri);
	$hed = substr ($uri, 0, strpos ($uri, '-'));
	$seo = substr ($uri, strpos ($uri, '-') + 1);
	if ($hed == '') $hed = 'sekcia';
	if ($seo == '') {
		$id = mysql_fetch_row (mysql_query ("SELECT id FROM {$prefix}_sec WHERE seo = '{$_CONFIG['homepage']}'"));
		$seo = $id[0].'-'.$_CONFIG['homepage'];
	}
	if (strpos ($seo, '-komentare-') !== false) {
		$pag = substr ($seo, strrpos ($seo, '-komentare-') + strlen ('-komentare-'));
		$seo = str_replace ('-komentare-' . $pag, '', $seo);
		global $title, $sep;
		$title .= $sep . langrep ('page', $pag);
	} else $pag = ceil ($kolko[0] / $param2);
	$linkhref = rwl ($hed, $seo);
} else {
	global $_REQUEST;
	if (strpos ($_REQUEST['img'], '-komentare-') !== false) {
		$pag = substr ($_REQUEST['img'], strrpos ($_REQUEST['img'], '-komentare-') + strlen ('-komentare-'));
	} else $pag = ceil ($kolko[0] / $param2);
	$linkhref = 'codes/pviewer/pviewer.php?img=' . str_replace ('-komentare-' . $pag, '', $_REQUEST['img']);
};
if ($kolko[0] > $param2) {
	$p = '<p align="center">' . n;
	for ($i = 1; $i <= ceil ($kolko[0] / $param2); ++$i) {
		if ($param3) $linktocoms = $linkhref . '-komentare-' . $i;
		else $linktocoms = rwl ($hed, $seo . '-komentare-' . $i);
		$p .= '<a href="' . $linktocoms . '#comments" title="' . langrep ('gopage', $i) . '" style="text-decoration:none;">';
		$p .= ($i == $pag) ? '<strong>[' . $i . ']</strong></a>' . n : '<strong>' . $i . '</strong></a>' . n;
	};
	$p .= '</p>' . n;
} else $p = '';
$out = '<a name="comments"></a>
<h1 align="center">' . $translate['comments'] . '</h1>' . n . $p;
if ($pag == 0) $pag = 1;
$db_select = $param2 * ($pag - 1);
$sql = @mysql_query ("SELECT `autor`, `text`, `mail`, `web`, DATE_FORMAT(`added`, '%d.%m.%Y @%H:%i'), `id`, `ip` FROM `{$prefix}_comments` WHERE `kde` = '$param1' ORDER BY `id` ASC LIMIT $db_select, $param2");
if (mysql_num_rows ($sql) != 0) {
	if (!$param3) $tohead = array_merge ($tohead, array ('<link rel="stylesheet" href="media/styling.php?what=comments" type="text/css" />'));
	$out .= '<script type="text/javascript">function doplnText (id,text){var obj=document.getElementById(id);if(obj){obj.value=text;}}</script>'.n;
	$TCID = 0;
	while ($tab = mysql_fetch_row ($sql)) {
		if ($tab[0] == 'admin') $meno = GetAuthorName (0);
		else if ($info = @mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_moderators` WHERE `nick` = '{$tab[0]}'"))) $meno = GetAuthorName ($info[0]);
		else {
			$meno = htmlspecialchars ($tab[0], ENT_QUOTES);
			if ($tab[3] != '') $meno = '<a href="' . str_replace ('http://http://', 'http://', 'http://' . $tab[3]) . '" rel="nofollow" target="_blank">' . $meno . '</a>';
			if (ONLINE) $meno .= ' <sub>' . implode (', ', array_filter (array ($tab[2], $tab[6]))) . '</sub>';
		};
		if ($tab[0] == 'admin') $OutMail = $_CONFIG['mail'];
		else if ($info = @mysql_fetch_row (@mysql_query ("SELECT `mail` FROM `{$prefix}_moderators` WHERE `nick` = '{$tab[0]}' LIMIT 1"))) $OutMail = $info[0];
		else $OutMail = $tab[2];
		$adminos = (ONLINE) ? '<a href="./?page=comment-edit&id='.$tab[5].'">' . $translate['edit'] . '</a>, <a href="./?page=comment-delete&id='.$tab[5].'">' . $translate['drop'] . '</a>' : '';
		$text = texyla ($tab[1], 'forum');
		while (false !== ($tagy[] = strpos ($text, '[reply:')) and false !== ($tagy[] = strpos ($text, ']', $tagy[0]))) {
			foreach ($tagy as $index => $value) $tags[$index] = '' . $value;
			$idof = substr ($text, $tags[0] + 7, $tags[1] - ($tags[0] + 7));
			unset ($tags, $tagy);
			if ($idinfo = mysql_fetch_row (mysql_query ("SELECT autor, text FROM {$prefix}_comments WHERE id = '$idof' LIMIT 1"))) {
				if ($idinfo[0] == 'admin') $idinfo[0] = GetAuthorName (0);
				else if ($info == @mysql_fetch_row (@mysql_query ("SELECT `id` FROM `{$prefix}_moderators` WHERE `nick` = $idinfo[0]"))) $idinfo[0] = GetAuthorName ($info[0]);
				else $idinfo[0] = $idinfo[0];
				@$idinfo[1] = texyla ('' . $idinfo[1], 'forum');
				$idinfo[1] = explode ("\n", $idinfo[1]);
				$idinfo[1] = implode ("\n\t", $idinfo[1]);
				$idinfo[1] = trim ($idinfo[1], "\n\t");
				$text = str_replace ('<p>[reply:' . $idof . ']', "<h4>" . langrep ('comments.replied', $idinfo[0]) . "</h4>\n<blockquote>\n\t" . $idinfo[1] . "\n</blockquote>\n<p>", $text);
				$text = str_replace ('[reply:' . $idof . ']', "</p>\n<blockquote>\n\t<h4>" . langrep ('comments.replied', $idinfo[0]) . "</h4>\n\t" . $idinfo[1] . "\n</blockquote>\n<p>", $text);
			} else $text = str_replace ('[reply:' . $idof . ']', '', $text);
		};
		if (isset ($template)) $out .= '<a name="comment-' . $tab[5] . '"></a>' . n . str_replace (array (
			'[author]',
			'[content]',
			'[gravatar]',
			'[datetime]',
			'[reply]',
			'[admin]',
			'[pairclass]'
		), array (
			$meno,
			str_replace ('<p></p>', '', $text),
			'media/gravatar.php?id=' . md5 ($OutMail),
			$tab[4],
			'<a href="#add-comment" onclick="reply.Texy.replaceSelection(\'**' . langrep ('comments.replyfor', strip_tags (htmlspecialchars ($tab[0], ENT_QUOTES))) . ':**\r\n\');">' . $translate['comments.reply'] . '</a>',
			(ONLINE) ? str_replace ('%', $adminos, $template->config['comments']['admin']) : '',
			(++$TCID % 2 == 0) ? str_replace ('%', $template->config['comments']['pairclass'], $template->config['comments']['classwriter']) : '',
		), $template->config['comments']['table']).n;
		else $out .= '<a name="comment-' . $tab[5] . '"></a>
<h3>'.$meno.'</h3>
<blockquote>'.str_replace ('<p></p>', '', $text).'</blockquote>
<p align="right">'.implode(', ',array_filter(array ($tab[4], '<a href="#add-comment" onclick="reply.Texy.replaceSelection(\'**' . langrep ('comments.replyfor', strip_tags (htmlspecialchars ($tab[0], ENT_QUOTES))) . ':**\r\n\');">' . $translate['comments.reply'] . '</a>', $adminos))).'</p>'.n;
	};
} else {
	$out .= "<p>{$translate['comments.empty']}</p>\n";
};
$out .= $p;
$out .= '<a name="add-comment"></a>
[hcm]add_com,' . $param1 . ',' . $linkhref . '[/hcm]';

?>