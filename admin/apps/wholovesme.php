<?php

class wholovesmeapp extends plugin {

	protected function load ()
	{
		$this -> title = 'Štatistiky vyhľadávania';
		$this -> version = '2.0';
		$this -> author = 'Tomáš Tatarko';
		$this -> url = 'http://tatarko.sk/';
		$this -> description = 'Pomocou tejto aplikácie môžete zistiť, z akých stránok prichádza najviac návštevníkov na Váš web a čo na ňom hľadajú.';
		$this -> modes = array ('application', 'widget', 'staticrun');
		$this -> cache = false;
		$this -> redactors = false;
		$this -> values = array ();
		$this -> tables = array (
			'referrers'	=> '`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `url` tinytext NULL DEFAULT NULL, `domain` tinytext NULL DEFAULT NULL, `time` int(11) unsigned DEFAULT 0, PRIMARY KEY (`id`)',
			'searches'	=> '`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `string` tinytext NULL DEFAULT NULL, `time` int(11) unsigned DEFAULT 0, PRIMARY KEY (`id`)',
		);
	}
	
	protected function application ()
	{
		global $prefix;
		if (isset ($_GET['domains']))
		{
			$return = '<ul>
 <li><a href="admin.php?app=' . $this -> apphash . '">Späť na úvod</a></li>' . "\n";
			$query = $this -> query ("SELECT `domain`, COUNT(`domain`) as `count` FROM `{prefix}referrers` GROUP BY `domain` ORDER BY `count` DESC, `id` DESC");
			while ($row = mysql_fetch_assoc ($query)) $return .= ' <li><a href="' . $row['domain'] . '" target="_blank">' . $row['domain'] . '</a> <em>(' . $row['count'] . 'x, <a href="admin.php?app=' . $this -> apphash . '&amp;domain=' . urlencode ($row['domain']) . '">detaily</a>)</em></li>' . n;
			return $return . "</ul>\n";
		}
		else if (isset ($_GET['searches']))
		{
			$return = '<ul>
 <li><a href="admin.php?app=' . $this -> apphash . '">Späť na úvod</a></li>' . "\n";
			$query = $this -> query ("SELECT `string`, COUNT(`string`) as `count` FROM `{prefix}searches` GROUP BY `string` ORDER BY `count` DESC, `id` DESC");
			while ($row = mysql_fetch_assoc ($query)) $return .= ' <li>' . $row['string'] . ' <em>(' . $row['count'] . 'x, <a href="admin.php?app=' . $this -> apphash . '&amp;search=' . urlencode ($row['string']) . '">detaily</a>)</em></li>' . n;
			return $return . "</ul>\n";
		}
		else if (isset ($_GET['domain']) and !empty ($_GET['domain']))
		{
			$return = '<ul>
 <li><a href="admin.php?app=' . $this -> apphash . '">Späť na úvod</a></li>' . "\n";
			$query = $this -> query ("SELECT `url`, `time` FROM `{prefix}referrers` WHERE `domain` = '" . adjust (urldecode ($_GET['domain'])) . "' ORDER BY `time` DESC");
			while ($row = mysql_fetch_assoc ($query)) $return .= ' <li><a href="' . $row['url'] . '" target="_blank">' . $row['url'] . '</a> <em>(' . date ('d.m.Y @H:i', $row['time']) . ')</em></li>' . n;
			return $return . "</ul>\n";
		}
		else if (isset ($_GET['internals']))
		{
			$return = '<ul>
 <li><a href="admin.php?app=' . $this -> apphash . '">Späť na úvod</a></li>' . "\n";
			$query = $this -> query ("SELECT DISTINCT `hodnota`, COUNT(`hodnota`) as `count` FROM `{$prefix}_iplog` WHERE `what` = 'search' GROUP BY `hodnota` ORDER BY `count` DESC, `id` DESC");
			while ($row = mysql_fetch_assoc ($query)) $return .= ' <li>' . $row['hodnota'] . ' <em>(' . $row['count'] . 'x)</em></li>' . n;
			return $return . "</ul>\n";
		}
		else if (isset ($_GET['search']) and !empty ($_GET['search']))
		{
			$return = '<ul>
 <li><a href="admin.php?app=' . $this -> apphash . '">Späť na úvod</a></li>' . "\n";
			$query = $this -> query ("SELECT `string`, `time` FROM `{prefix}searches` WHERE `string` = '" . adjust (urldecode ($_GET['search'])) . "' ORDER BY `time` DESC");
			while ($row = mysql_fetch_assoc ($query)) $return .= ' <li><a href="http://www.google.com/search?ie=UTF-8&q=' . urlencode ($row['string']) . '" target="_blank">' . $row['string'] . '</a> <em>(' . date ('d.m.Y @H:i', $row['time']) . ')</em></li>' . n;
			return $return . "</ul>\n";
		}
		else 
		{
			$return = '<h2>Odkazujúce stránky</h2>
<p>Ľudia prichádzajú na Vašu stránku z nasledujúcich domén:</p>
<ul>'.n;
			$query = $this -> query ("SELECT `domain`, COUNT(`domain`) as `count` FROM `{prefix}referrers` GROUP BY `domain` ORDER BY `count` DESC, `id` DESC LIMIT 5");
			while ($row = mysql_fetch_assoc ($query)) $return .= '<li><a href="' . $row['domain'] . '" target="_blank">' . $row['domain'] . '</a> <em>(' . $row['count'] . 'x, <a href="admin.php?app=' . $this -> apphash . '&amp;domain=' . urlencode ($row['domain']) . '">detaily</a>)</em></li>' . n;
			if (mysql_num_rows ($query) == 5) $return .= ' <li><a href="admin.php?app=' . $this -> apphash . '&amp;domains">Zobraziť všetky záznamy</a></li>' . "\n";
			$return .= '</ul>
<h2>Vyhľadávanie Google</h2>
<p>Ľudia prišli na Vašu stránku pri vyhľadávaní nasledujúcich fráz:</p>
<ul>'.n;
			$query = $this -> query ("SELECT `string`, COUNT(`string`) as `count` FROM `{prefix}searches` GROUP BY `string` ORDER BY `count` DESC, `id` DESC LIMIT 5");
			while ($row = mysql_fetch_assoc ($query)) $return .= '<li>' . $row['string'] . ' <em>(' . $row['count'] . 'x, <a href="admin.php?app=' . $this -> apphash . '&amp;search=' . urlencode ($row['string']) . '">detaily</a>)</em></li>' . n;
			if (mysql_num_rows ($query) == 5) $return .= ' <li><a href="admin.php?app=' . $this -> apphash . '&amp;searches">Zobraziť všetky záznamy</a></li>' . "\n";
			$return .= '</ul>
<h2>Interné vyhľadávanie</h2>
<p>Ľudia na Vašej stránke hľadali nasledujúce frázy:</p>
<ul>'.n;
			$sql = mysql_query ("SELECT DISTINCT `hodnota`, COUNT(`hodnota`) as `count` FROM `{$prefix}_iplog` WHERE `what` = 'search' GROUP BY `hodnota` ORDER BY `count` DESC, `id` DESC LIMIT 5");
			while ($data = mysql_fetch_assoc ($sql))
			$return .= '<li>' . $data['hodnota'] . ' <em>(' . $data['count'] . 'x)</em></li>'.n;
			if (mysql_num_rows ($sql) == 5) $return .= ' <li><a href="admin.php?app=' . $this -> apphash . '&amp;internals">Zobraziť všetky záznamy</a></li>' . "\n";
			return $return . '</ul>';
		}
	}

	protected function staticrun ()
	{
		if (isset ($_SERVER['HTTP_REFERER'])
		and !empty ($_SERVER['HTTP_REFERER'])
		and $domain = substr ($_SERVER['HTTP_REFERER'], 0, strpos ($_SERVER['HTTP_REFERER'], '/', 7) + 1)
		and $domain != _SiteLink) {
			if (strpos ($domain, 'google') !== false) {
				$var = parse_url ($_SERVER['HTTP_REFERER']);
				foreach (explode ('&', urldecode ($var['query'])) as $var) {
					$var = explode ('=', $var);
					$name = $var[0];
					unset ($var[0]);
					$vars[$name] = implode ('=', $var);
				};
				if (isset ($vars['q']) and !empty ($vars['q'])) {
					global $out;
					str_ireplace ($vars['q'], '<span class="highlighter">' . strtoupper($vars['q']) . '</span>', $out);
					$this -> query ("INSERT INTO `{prefix}searches` VALUES (0, '" . adjust ($vars['q']) . "', UNIX_TIMESTAMP());");
				} else $this -> query ("INSERT INTO `{prefix}referrers` VALUES (0, '" . adjust ($_SERVER['HTTP_REFERER']) . "', '" . adjust ($domain) . "', UNIX_TIMESTAMP());");
			} else $this -> query ("INSERT INTO `{prefix}referrers` VALUES (0, '" . adjust ($_SERVER['HTTP_REFERER']) . "', '" . adjust ($domain) . "', UNIX_TIMESTAMP());");
		};
	}
	
	protected function widget ()
	{
		global $prefix;
		$return = '<h4>Odkazujúce stránky</h4>
<ul>'.n;
			$query = $this -> query ("SELECT `domain`, COUNT(`domain`) as `count` FROM `{prefix}referrers` GROUP BY `domain` ORDER BY `count` DESC, `id` DESC LIMIT 3");
			while ($row = mysql_fetch_assoc ($query)) $return .= '<li><a href="' . $row['domain'] . '" target="_blank">' . $row['domain'] . '</a> <em>(' . $row['count'] . 'x)</em></li>' . n;
			$return .= '</ul>
<h4>Vyhľadávanie Google</h4>
<ul>'.n;
			$query = $this -> query ("SELECT `string`, COUNT(`string`) as `count` FROM `{prefix}searches` GROUP BY `string` ORDER BY `count` DESC, `id` DESC LIMIT 3");
			while ($row = mysql_fetch_assoc ($query)) $return .= '<li>' . $row['string'] . ' <em>(' . $row['count'] . 'x, <a href="admin.php?app=' . $this -> apphash . '&amp;search=' . urlencode ($row['string']) . '">detaily</a>)</em></li>' . n;
			$return .= '</ul>
<h4>Interné vyhľadávanie</h4>
<ul>'.n;
			$sql = mysql_query ("SELECT DISTINCT `hodnota`, COUNT(`hodnota`) as `count` FROM `{$prefix}_iplog` WHERE `what` = 'search' GROUP BY `hodnota` ORDER BY `count` DESC, `id` DESC LIMIT 3");
			while ($data = mysql_fetch_assoc ($sql))
			$return .= '<li>' . $data['hodnota'] . ' <em>(' . $data['count'] . 'x)</em></li>'.n;
			return $return . '</ul>';
	}
};

?>