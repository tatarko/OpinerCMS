<?php



class TemplateClass {

	// Premenné
	public $name;
	public $linker;
	public $config;
	public $menu;
	public $index = true;
	private $dir;



	// Konštruktor
	public function __construct ($name) {
		$this->name = $name;
		$this->dir = str_replace (array ('admin', 'includes', '\\\\', '//'), '', dirname (__FILE__)) . '/templates/' . $name . '/';
		$this->linker = _SiteLink . 'templates/' . $name . '/';
		if (file_exists ($this->dir . 'body.php') and file_exists ($this->dir . 'config.php')) {
			include ($this->dir . 'config.php');
			$this->config = $TempConfig;
			return true;
		} else return false;
	}



	// Výstup
	public function EchoTemplate () {
		include ($this->dir . 'body.php');
	}



	// Hlavička
	public function head () {
		global $_CONFIG, $tohead, $title, $_META, $translate;
		if (!is_array ($tohead)) $tohead = array ($tohead);
		$tohead = array_merge ($tohead, array (
		'<link rel="stylesheet" href="media/default.css" type="text/css" />',
		'<link rel="stylesheet" href="' . $this->linker . 'style.css" type="text/css" id="css1" />',
		'<link rel="alternate" type="application/rss+xml" href="rss.php" title="' . $translate['lastarts'] . '" />',
		'<link rel="alternate" type="application/rss+xml" href="rss.php?what=news" title="'.$_CONFIG['microblog_head'].'" />',
		'<link rel="alternate" type="application/rss+xml" href="rss.php?what=komentare" title="' . $translate['lastcomments'] . '" />'));
		if ($_CONFIG['favicon'] != '') $tohead = array_merge ($tohead, array (
		'<link rel="shortcut icon" href="media/'.$_CONFIG['favicon'].'" type="'.$_CONFIG['favicon_mime'].'" />'));
		$tohead = array_unique ($tohead);
		if (array_search ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>', $tohead) !== false) {
			$id = array_search ('<script type="text/javascript" src="codes/texyla/texyla.js"></script>', $tohead);
			unset ($tohead[$id]);
			$tohead[] = '<script type="text/javascript" src="codes/texyla/texyla.js"></script>';
		};
		$tohead = implode ("\n", $tohead).n;
		if ($_CONFIG['title_reverse'] == 1) {
			$title = substr ($title, strlen ($_CONFIG['title'] . ' ' . $_CONFIG['sep'] . ' '));
			$title .= ' ' . $_CONFIG['sep'] . ' ' . $_CONFIG['title'];
		};
		echo (MOBILE) ? '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">' : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		list ($system, $version) = explode('~$~', SystemInfo);
		echo '
<html xmlns="http://www.w3.org/1999/xhtml" lang="sk">
<head>
<title>'.$title.'</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="keywords" content="' . setQ (((isset ($_META['keywords'])) ? $_META['keywords'] : $_CONFIG['keys'])) . '" />
<meta name="description" content="' . setQ (((isset ($_META['description'])) ? $_META['description'] : $_CONFIG['desc'])) . '" />
<meta name="copyright" content="' . setQ ($_CONFIG['author']) . '" />
<meta name="author" content="' . setQ ($_CONFIG['author']) . '" />
<meta name="generator" content="' . $system . ' ' . $version . '" />
<meta name="robots" content="' . (($this -> index) ? 'Index, Follow' : 'Noindex, Nofollow') . '" />
' . $tohead;
	}



	// Názov stránky
	public function title () {
		global $_CONFIG, $translate;
		echo '<a href="./" title="' . $translate['gohome'] . '">' . $_CONFIG['title'] . '</a>';
	}



	// Popis stránky
	public function desc () {
		global $_CONFIG;
		echo $_CONFIG['desc'];
	}



	// Menu
	public function menu ($id) {
		if (isset ($this->menu["menu$id"]))
		echo $this->menu["menu$id"];
	}



	// Telo Stránky
	public function content () {
		global $out, $translate;
		$out = (isset ($out)) ? $out : '<h1 align="center">' . $translate['error'] . ' 404</h1>
		<p>' . $translate['wrongreq'] . '</p>';
		echo $out;
	}



	// Pätička stránky
	public function foot () {
		global $foot_info, $_CONFIG, $online, $stats, $translate;
		$foot_info = str_replace ("\n", '[br /]', $_CONFIG['foot']);
		$foot_info = OpinerAutoLoader::texyla ($foot_info, 'admin');
		$foot_info = str_replace ('<p>', '', $foot_info);
		$foot_info = str_replace ('</p>', '', $foot_info);
		$foot_info = str_replace ('[br /]', '<br />', $foot_info);
		$foot_info = str_replace ('<br>', '', $foot_info);
		$foot_info = str_replace ('<br /><br />', '<br />', $foot_info);
		if (substr ($foot_info, 0, 6) == '<br />') $foot_info = substr ($foot_info, 6);
		if (substr ($foot_info, -6, 6) == '<br />') $foot_info = substr ($foot_info, 0, -6);
		$foot_info .= '<br /><a href="http://opiner.tatarko.sk/" target="_blank" title="' . $translate['creator.title'] . '">Opiner CMS</a>';
		$foot_info .= ' &bull; <a href="' . $this->config['info']['link'] . '" target="_blank">' . $this->config['info']['author'] . '</a>';
		if (!ONLINE and $_CONFIG['admin_foot_link'] == 1) $foot_info .= ' &bull; <a href="login.php">' . $translate['controlpanel'] . '</a>';
		if (ONLINE) $foot_info .= ' &bull; <a href="admin.php">' . $translate['controlpanel'] . '</a>';
		echo HcmParser (str_replace ('[stats]', '[hcm]stats,0[/hcm]', $foot_info));
	}
	
	
	
	/*--- Feedback pre MainMenu ---*/
	
	private function bzf ($int) {
		$len = strlen ($int);
		$bigint = '00000000000000000000';
		return substr ($bigint, 0, -$len) . $int;
	}



	// Hlavné menu
	public function MainMenu ($id) {
		global $_CONFIG, $prefix, $_GET, $_REQUEST, $translate;
		$_TEMP = $this->config["menu$id"];
		$mainmenu = $_TEMP['list-start'].n;
		$sql = @mysql_query ("SELECT `nadpis`, `seo`, `id`, `position` FROM `{$prefix}_sec` WHERE `msec` = 0 ORDER BY `position` ASC");
		while ($tab = @mysql_fetch_row ($sql)) {
			$start = (isset ($_GET['sekcia'], $_TEMP['link-active']) and $_GET['sekcia'] == $tab[2] . '-' . $tab[1]) ? $_TEMP['link-active'] : $_TEMP['link-start'];
			$array[$this->bzf($tab[3]).$this->bzf($tab[2]).'a'] = $start . '<a href="' . rwl ('sekcia', $tab[2] . '-' . $tab[1]) . '" title="' . langrep ('gosection', $tab[0]) . '">' . $tab[0] . '</a>' . $_TEMP['link-end'] . n;
		};
		$sql = @mysql_query ("SELECT `nadpis`, `skr`, `id`, `position` FROM `{$prefix}_cats` WHERE `inmenu` = 1 ORDER BY `position` ASC");
		while ($tab = @mysql_fetch_row ($sql)) {
			$start = (isset ($_GET['kategoria'], $_TEMP['link-active']) and $_GET['kategoria'] == $tab[2] . '-' . $tab[1]) ? $_TEMP['link-active'] : $_TEMP['link-start'];
			$array[$this->bzf($tab[3]).$this->bzf($tab[2]).'b'] = $start . '<a href="' . rwl ('kategoria', $tab[2] . '-' . $tab[1]) . '" title="' . langrep ('gosection', $tab[0]) . '">' . $tab[0] . '</a>' . $_TEMP['link-end'] . n;
		};
		list ($id, $seo) = @mysql_fetch_row (@mysql_query ("SELECT `id`, `seo` FROM `{$prefix}_sec` WHERE `id` = {$_CONFIG['homepage']} LIMIT 1"));
		$homepage = "$id-$seo";
		$arr = array (
			'[archive]' => array ('archiv', 1),
			'[catlist]' => array ('stranka', 'archiv'),
			'[gallery]' => array ('gallery', 1),
			'[gbook]' => array ('kniha', 1),
			'[sitemap]' => array ('stranka', 'sitemap'),
			'[rss]' => array ('stranka', 'rss'),
			'[home]' => array ('sekcia', $homepage),
		);
		$sql = @mysql_query ("SELECT `name`, `title`, `target`, `href`, `id`, `position` FROM `{$prefix}_links` WHERE `location` = 1 ORDER BY `position` ASC");
		while ($tab = @mysql_fetch_row ($sql)) {
			if (isset ($arr[$tab[3]])) {
				unset ($_GET[array_search($homepage,$_GET)]);
				if ($tab[3] == '[home]' and empty ($_GET))
				$array[$this->bzf($tab[5]).$this->bzf($tab[4]).'c'] = (isset($_TEMP['link-active'])?$_TEMP['link-active']:$_TEMP['link-start']). '<a href="./" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else if ($tab[3] == '[home]') $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start']. '<a href="./" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else if (isset ($_REQUEST[$arr[$tab[3]][0]], $_TEMP['link-active']) and (is_int ($arr[$tab[3]][1]) or $_REQUEST[$arr[$tab[3]][0]] == $arr[$tab[3]][1]))
				$array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-active']. '<a href="' . rwl ($arr[$tab[3]][0], $arr[$tab[3]][1]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start']. '<a href="' . rwl ($arr[$tab[3]][0], $arr[$tab[3]][1]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
			} else if (preg_match ('#\[app:([a-zA-Z0-9_]*)\]#', $tab[3], $match, PREG_OFFSET_CAPTURE)) {
				if (isset ($_REQUEST['plugin']) and $_REQUEST['plugin'] == $match[1][0])
				$array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = ((isset($_TEMP['link-active'])) ? $_TEMP['link-active'] : $_TEMP['link-start']) . '<a href="' . rwl ('plugin', $match[1][0]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
				else $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start']. '<a href="' . rwl ('plugin', $match[1][0]) . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'].n;
			} else $array[$this->bzf($tab[5]).$this->bzf($tab[4]).'l'] = $_TEMP['link-start'] . '<a href="' . $tab[3] . '" title="' . $tab[1] . '" target="' . $tab[2] . '">' . $tab[0] . '</a>' . $_TEMP['link-end'] . n;
		};
		ksort ($array);
		$mainmenu .= implode ('', $array);
		$mainmenu .= $_TEMP['list-end'].n;
		return $mainmenu;
	}



	// Vyhľadávanie
	public function GetSearchBox ($id) {
		if (isset ($this->config["menu$id"]['box-search']))
		return $this->config["menu$id"]['box-search'];
		else return false;
	}
};
?>