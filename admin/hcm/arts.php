<?php

$sql = "SELECT `seo`, `nadpis`, DATE_FORMAT(`added`, '%d%m%y%H%i%s'), `perex`, `id`, `cat`, `reads`, SUBSTR(text, 1, 1), `coms`, `cat2`, `cat3`, `imagelink`, `autor` FROM `{$prefix}_clanky` WHERE `showing` = 1 AND `added` <= NOW() AND `confirmed` = 1[w] ORDER BY `added` DESC LIMIT [l]";
if (isset ($param2)) $param2 = str_replace('ciarka', ', ', $param2);
if (isset ($param2) and $param2 != '') $sql = str_replace ('[l]', $param2, $sql); else $sql = str_replace ('[l]', $_CONFIG['list_arts'], $sql);
if (isset ($param1) and $param1 != '') $sql = str_replace ('[w]', " AND (`cat` = '$param1' OR `cat2` = '$param1' OR `cat3` = '$param1')", $sql); else $sql = str_replace ('[w]', '', $sql);
$s = mysql_query ($sql);
if (mysql_num_rows ($s) != 0) {
	$out = '';
	if ($_CONFIG['googleplus'] and !MOBILE and $_CONFIG['wysiwyg'] != 'texyla')
	$tohead[] = '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"> {lang: "sk"} </script>';
	while ($tab = mysql_fetch_row ($s)) {
		$kolko = mysql_fetch_row (mysql_query ("SELECT COUNT(*) FROM {$prefix}_comments WHERE kde = 'clanok_$tab[4]'"));
		if ($tab[11] != '') $imagelink = '<img src="'.((MOBILE)?'media/image.php?file='.$tab[11]:$tab[11]).'" class="articleImage" alt="' . setQ($tab[0]) . '" />'.n;
		else if ($imagecat1 = mysql_fetch_row (mysql_query ("SELECT imagelink FROM {$prefix}_cats WHERE id = '{$tab[5]}'")) and $imagecat1[0] != '') $imagelink = '<img src="'.$imagecat1[0].'" class="articleImage" alt="' . setQ($tab[0]) . '" />';
		else if ($imagecat2 = mysql_fetch_row (mysql_query ("SELECT imagelink FROM {$prefix}_cats WHERE id = '{$tab[9]}'")) and $imagecat2[0] != '') $imagelink = '<img src="'.$imagecat2[0].'" class="articleImage" alt="' . setQ($tab[0]) . '" />';
		else if ($imagecat3 = mysql_fetch_row (mysql_query ("SELECT imagelink FROM {$prefix}_cats WHERE id = '{$tab[10]}'")) and $imagecat3[0] != '') $imagelink = '<img src="'.$imagecat3[0].'" class="articleImage" alt="' . setQ($tab[0]) . '" />';
		else $imagelink = '';
		$imagend = ($imagelink != '') ? n.'<div class="cleanL"></div>' : '';
		if ($kolko[0] > 0) {
			$last = mysql_fetch_row (mysql_query ("SELECT id FROM {$prefix}_comments WHERE kde = 'clanok_$tab[4]' ORDER BY id DESC LIMIT 1"));
			$commlink = '#comment-' . $last[0];
		} else $commlink = '#add-comment';
		if (!isset ($_COOKIE['read-article-' . $tab[4]]) and $tab[7] == '') {
			SetCookie ('read-article-' . $tab[4], 1, time () + (60 * 30));
			mysql_query ("UPDATE {$prefix}_clanky SET `reads` = `reads` + 1 WHERE id = '$tab[4]' LIMIT 1");
		};
		$artlink = ($tab[7] == '') ? '' : '<p><a href="'.rwl('clanok',$tab[4].'-'.$tab[0]).'#readmore">' . $translate['readmore'] . '</a></p>';
		$text = OpinerAutoLoader::texyla (HcmParser ($tab[3]), 'admin');
		$text .= $artlink;
		$cat = mysql_fetch_row (mysql_query ("SELECT skr, nadpis FROM {$prefix}_cats WHERE id=$tab[5]"));
		$datetime = mktime (substr($tab[2],6,2), substr($tab[2],8,2), substr($tab[2],10,2), substr($tab[2],2,2), substr($tab[2],0,2), substr($tab[2],4,2));
		$commlink = '<a href="' . rwl ('clanok', $tab[4].'-'.$tab[0]) . $commlink . '">' . langrep ('commentsx', $kolko[0]) . '</a>';
		$commbool = ($_CONFIG['global_comments'] == 1 and $tab[8] == 1) ? true : false;
		if ($_CONFIG['global_voting']) {
			$hodnotenie = @mysql_fetch_row (@mysql_query ("SELECT COUNT(hodnota) as kolko, SUM(hodnota) FROM {$prefix}_iplog WHERE what = 'hodnotenie_art_$tab[4]'"));
			if ($hodnotenie[0] == 0) $hodnotenie = array (0, 0);
			else $hodnotenie = array ($hodnotenie[0], round ($hodnotenie[1]/$hodnotenie[0],0));
			$rating = langrep ('ratingx', $hodnotenie[1]);
		};
		$out .= str_replace (array (
			'[head]',
			'[author]',
			'[categories]',
			'[comments]',
			'[reads]',
			'[rating]',
			'[content]',
		), array (
			'<a href="' . rwl ('clanok', $tab[4].'-'.$tab[0]) . '">' . $tab[1] . '</a>',
			($_CONFIG['global_author'] == 1) ? str_replace ('%', GetAuthorName ($tab[12]), $template->config['arts']['author']) : '',
			GetCatsList ($tab[5], $tab[9], $tab[10]),
			($commbool) ? str_replace ('%', $commlink, $template->config['arts']['comments']) : '',
			($_CONFIG['global_reads']) ? str_replace ('%', oddel ($tab[6]) . 'x prečítané', $template->config['arts']['reads']) : '',
			($_CONFIG['global_voting']) ? str_replace ('%', $rating, $template->config['arts']['rating']) : '',
			$imagelink . $text . $imagend . (($_CONFIG['wysiwyg'] == 'texyla') ? '' : twitterButton (_SiteLink . rwl ('clanok', $tab[4] . '-' . $tab[0]), $tab[1])) . (($_CONFIG['googleplus'] == 1 and !MOBILE and $_CONFIG['wysiwyg'] != 'texyla') ? '<g:plusone size="medium" href="' . _SiteLink . rwl ('clanok', $tab[4] . '-' . $tab[0]) . '"></g:plusone>' . "\n" : '') . (($_CONFIG['facebooklike'] == 1) ? '<iframe src="http://www.facebook.com/widgets/like.php?href=' . urlencode (_SiteLink . rwl ('clanok', $tab[4] . '-' . $tab[0])) . '" scrolling="no" frameborder="0" style="border:none; width:450px; height:25px"></iframe>' . n : ''),
		), mdf ($template->config['arts']['table'], $datetime)).n;
	};
} else $out = '';

?>