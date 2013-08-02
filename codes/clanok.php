<?php
if (!defined ('in')) exit();



/*--- Informácie z query ---*/

$query = explode ('-', $_GET['clanok']);
$id = adjust ($query[0], true);
$pag = (false !== ($pos = array_search ('komentare', $query)) and $pos == (count ($query) - 2)) ? ($query[(count ($query) - 1)]) : 1;



/*--- Práca s článkom ---*/

if ($info = mysql_fetch_row(mysql_query("SELECT nadpis,text,cat,UNIX_TIMESTAMP(`added`),perex,seo,showing,`reads`,coms,cat2,cat3,imagelink,UNIX_TIMESTAMP(`added`), `tags`, `autor`, `confirmed`, `album` FROM {$prefix}_clanky WHERE id = '$id' LIMIT 1"))) {
	if ($info[6] == 1 or ONLINE) {
	if ($info[12] <= time() or ONLINE) {
	if ($info[15] == 1 or ONLINE) {

		mb_internal_encoding ('UTF-8');
		if (!isset ($_COOKIE['read-article-' . $id])) {
			SetCookie ('read-article-' . $id, 1, time () + 3600);	// Vloží Cookies na jednu hodinu
			mysql_query ("UPDATE {$prefix}_clanky SET `reads` = `reads` + 1 WHERE id = '$id' LIMIT 1");
		};
		if ($info[11] != '') {
			$imagelink = '<img src="'.((MOBILE)?'media/image.php?file='.$info[11]:$info[11]).'" class="articleImage" alt="' . setQ($info[0]) . '" />'.n;
			$siteimage = strpos ($info[11], 'http://') === false ? _SiteLink . $info[11] : $info[11];
		} else if ($imagecat1 = mysql_fetch_row (mysql_query ("SELECT imagelink FROM {$prefix}_cats WHERE id = '{$info[5]}'")) and $imagecat1[0] != '') {
			$imagelink = '<img src="'.$imagecat1[0].'" class="articleImage" alt="' . setQ($info[0]) . '" />';
			$siteimage = strpos ($imagecat1[0], 'http://') === false ? _SiteLink . $imagecat1[0] : $imagecat1[0];
		} else if ($imagecat2 = mysql_fetch_row (mysql_query ("SELECT imagelink FROM {$prefix}_cats WHERE id = '{$info[9]}'")) and $imagecat2[0] != '') {
			$imagelink = '<img src="'.$imagecat2[0].'" class="articleImage" alt="' . setQ($info[0]) . '" />';
			$siteimage = strpos ($imagecat2[0], 'http://') === false ? _SiteLink . $imagecat2[0] : $imagecat2[0];
		} else if ($imagecat3 = mysql_fetch_row (mysql_query ("SELECT imagelink FROM {$prefix}_cats WHERE id = '{$info[10]}'")) and $imagecat3[0] != '') {
			$imagelink = '<img src="'.$imagecat3[0].'" class="articleImage" alt="' . setQ($info[0]) . '" />';
			$siteimage = strpos ($imagecat2[0], 'http://') === false ? _SiteLink . $imagecat2[0] : $imagecat2[0];
		} else {
			$imagelink = '';
			$siteimage = $_CONFIG['favicon'] != '' ? _SiteLink . 'media/' . $_CONFIG['favicon'] : 'http://feedback.opiner-cms.net/article.png';
		};
		$imagend = ($imagelink != '') ? n.'<div class="cleanL"></div>' : '';
		$comments = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_comments WHERE kde='clanok_$id'"));
		$hodnotenie = @mysql_fetch_row (@mysql_query ("SELECT COUNT(hodnota) as kolko, SUM(hodnota) FROM {$prefix}_iplog WHERE what = 'hodnotenie_art_$id'"));
		if ($hodnotenie[0] == 0) $hodnotenie = array (0, 0);
		else $hodnotenie = array ($hodnotenie[0], round ($hodnotenie[1]/$hodnotenie[0],0));
		$commbool = ($_CONFIG['global_comments'] == 1 and $info[8] == 1) ? true : false;
		$title .= $sep . $info[0];
		$perexout = (($_CONFIG['wysiwyg'] == 'texyla') ? OpinerAutoLoader::texyla (HcmParser ($info[4], false, $info[14]), 'admin') : HcmParser ($info[4], false, $info[14]));
		$ServiceLink = _SiteLink . rwl ('clanok', $id . '-' . $info[5]);
		$out = '<h1 align="center">' . $info[0] . '</h1>'.n;
		$out .= '<blockquote>' . $imagelink . $perexout . $imagend . "</blockquote>\n";
		$out .= '<a name="read-more"></a>'.n;
		$out .= ($_CONFIG['wysiwyg'] == 'texyla') ? OpinerAutoLoader::texyla (HcmParser ($info[1], false, $info[14]), 'admin') . '<br />'.n : HcmParser ($info[1], false, $info[14]) . '<br />'.n;
		
		if ($info[16] > 0) {
			$out .= HcmParser ('[hcm]gall_imgs,' . $info[16] . '[/hcm]');
		}

		$out .= '<hr width="100%" />'.n;
		$out .= "<table class=\"infobox\">\n <tr><td>\n  <table>\n   <tr><td>" . $translate['arts.date'] . '</td><td>' . mdf ('%l, %d. %F, %Y @%H:%i', $info[3]) . '</td></tr>'.n;
		if ($_CONFIG['global_author'] == 1) $out .= '   <tr><td>' . $translate['arts.author'] . '</td><td>' . GetAuthorName ($info[14]) . '</td></tr>'.n;
		$out .= '   <tr><td>' . $translate['categories'] . '</td><td>' . GetCatsList ($info[2], $info[9], $info[10]) . "</td></tr>\n <tr><td>" . $translate['arts.tags'] . '</td><td>' . getTags ($info[13]) . '</td></tr>'.n;
		if ($commbool) $out .= '   <tr><td>' . $translate['commented'] . '</td><td><a href="#comments">'.$comments[0].'x</a></td></tr>'.n;
		if ($_CONFIG['global_reads'] == 1) $out .= '   <tr><td>' . $translate['arts.readed'] . '</td><td>'.oddel ($info[7]) . 'x</td></tr>'.n;
		if ($_CONFIG['global_voting']) {
			$out .= '   <tr><td>' . $translate['arts.voting'] . '</td><td>' . $hodnotenie[1] . '% (' . $hodnotenie[0] . 'x)';
			if (!@mysql_fetch_row (@mysql_query ("SELECT id FROM {$prefix}_iplog WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND what = 'hodnotenie_art_$id' LIMIT 1"))) {
				$out .= '   <form action="./" method="post" />
    <input type="hidden" name="page" value="article-votings" />
    <input type="hidden" name="ArtID" value="' . $id . '" />
    <select name="PerHight">'.n;
				for ($i = 0; $i <= 100; $i += 10) $out .= '     <option value="'.$i.(($i == 50)?'" selected="selected':'').'">'.$i.'%</option>'.n;
				$out .= '    </select>
    <input type="submit" name="ok" value="' . $translate['submit'] . '" />
    </form>'.n;
			};
			$out .= "   </td></tr>\n";
		};
		$out .= "  </table>\n";
		$query1 = @mysql_query ("SELECT `id`, `nadpis`, `seo` FROM `{$prefix}_clanky` WHERE `id` != $id AND (`tags` LIKE '%" . implode ("%' OR `tags` LIKE '%", explode (', ', $info[13])) . "%') AND `added` <= NOW() AND `showing` = 1 AND `confirmed` = 1 ORDER BY MATCH(`tags`) AGAINST('" . adjust ($info[13]) . "' IN BOOLEAN MODE) DESC LIMIT 3");
		$query2 = @mysql_query ("SELECT `id`, `nadpis`, `seo` FROM `{$prefix}_clanky` WHERE `id` != $id AND UNIX_TIMESTAMP(`added`) <= $info[12] + 604800 AND UNIX_TIMESTAMP(`added`) >= $info[12] - 604800 AND `added` <= NOW() AND `showing` = 1 AND `confirmed` = 1 ORDER BY ABS($info[12] - UNIX_TIMESTAMP(`added`)) ASC LIMIT 3");
		if (($_CONFIG['similararts'] == 1 and @mysql_num_rows ($query1) > 0) or  ($_CONFIG['sameoldarts'] == 1 and @mysql_num_rows ($query2) > 0)) {
			$out .= " </td><td>\n";
			if ($_CONFIG['similararts'] == 1 and @mysql_num_rows ($query1) > 0) {
				$out .= "  <h4>{$translate['arts.relative']}</h4>\n  <ul>\n";
				while ($ar = @mysql_fetch_assoc ($query1)) $out .= '   <li><a href="' . rwl ('clanok', $ar['id'] . '-' . $ar['seo']) . '">' . $ar['nadpis'] . "</a></li>\n";
				$out .= "  </ul>\n";
			};
			if ($_CONFIG['sameoldarts'] == 1 and @mysql_num_rows ($query2) > 0) {
				$out .= "  <h4>{$translate['arts.sametime']}</h4>\n  <ul>\n";
				while ($ar = @mysql_fetch_assoc ($query2)) $out .= '   <li><a href="' . rwl ('clanok', $ar['id'] . '-' . $ar['seo']) . '">' . $ar['nadpis'] . "</a></li>\n";
				$out .= "  </ul>\n";
			};
		};
		$out .= " </td></tr>\n</table>\n";
		if ($_CONFIG['global_linkers']) $out .= getLinkServicies (urldecode($ServiceLink), urldecode($info[0])) . '<br />' . n;
		if ($_CONFIG['googleplus'] == 1){
			$tohead[] = '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"> {lang: "sk"} </script>';
			$out .= '<g:plusone size="medium" href="' . $ServiceLink . '"></g:plusone>';
		};
		$out .= twitterButton ($ServiceLink, $info[0]);
		if ($_CONFIG['facebooklike'] == 1) $out .= '<iframe src="http://www.facebook.com/widgets/like.php?href=' . urlencode ($ServiceLink) . '" scrolling="no" frameborder="0" style="border:none; width:450px; height:25px"></iframe><br /><br />' . n;
		if ($commbool) $out .= HcmParser ('[hcm]com,clanok_'.$id.'[/hcm]');
		$_META['keywords'] = $info[13];
		$_META['description'] = preg_replace ('#[\s]+#', ' ', strip_tags ((mb_strlen ($perexout) > 96) ? mb_substr ($perexout, 0, mb_strpos ($perexout, ' ', 96)) . ' (...)' : $perexout));
		if (substr ($_META['description'], 0, 1) == ' ') $_META['description'] = substr ($_META['description'], 1);
		$tohead[] = '<meta property="og:image" content="' . $siteimage . '" />';
		$tohead[] = '<meta property="og:site_name" content="' . setQ ($_CONFIG['title']) . '" />';
		$tohead[] = '<meta property="og:title" content="' . setQ ($info[0]) . '" />';
		$tohead[] = '<meta property="og:description" content="' . setQ ($_META['description']) . '" />';
		$tohead[] = '<meta property="og:url" content="' . $ServiceLink . '" />';
		$tohead[] = '<meta property="og:type" content="article" />';
	} else {
		$out = "<p>{$translate['er4']}</p>";
		header("HTTP/1.0 404 Not Found");
	};
	} else {
		$out = "<p>{$translate['er5']}</p>";
		header("HTTP/1.0 404 Not Found");
	};
	} else {
		$out = "<p>{$translate['er6']}</p>";
		header("HTTP/1.0 404 Not Found");
	};
} else {
	$out = "<p>{$translate['er7']}</p>";
	header("HTTP/1.0 404 Not Found");
};
?>lse {
		$out = "<p>{$translate['er6']}</p>";
		header("HTTP/1.0 404 Not Found");
	};
} else {
	$out = "<p>{$translate['er7']}</p>";
	header("HTTP/1.0 404 Not Found");
};
?>