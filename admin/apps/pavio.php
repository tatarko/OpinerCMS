<?php

class pavioapp extends plugin {

	protected function load () {
		$this -> title = 'Pavio';
		$this -> version = '1.0';
		$this -> author = 'Tomáš Tatarko';
		$this -> url = 'http://opiner.tatarko.sk/';
		$this -> description = 'Prehľad hlášok pridaných k fotografiám v galérií';
		$this -> modes = array ('plugin', 'hcm');
		$this -> cache = false;
		$this -> redactors = false;
		$this -> values = array ();
		$this -> tables = array ();
	}
	
	protected function plugin () {
		global $prefix, $tohead, $_CONFIG, $translate;
		if ($_CONFIG['imgbrowser'] != 'pavio') return '<p class="error">Pavio is not default image browser!</p>';
		if (false === ($browser = loadImageBrowser ('sidenotes'))) return '<p class="error">Unable to load pavio class!</p>';
		
		$out = '';
		$sql = mysql_query ("SELECT DISTINCTROW `a`.`id`, `a`.`type`, `a`.`nadpis`, `a`.`popis` FROM `{$prefix}_img` as `a`, `{$prefix}_comments` as `b` WHERE SUBSTRING(`b`.`kde`, 1, 6) = 'image_' AND SUBSTRING(`b`.`kde`, 7) = `a`.`id` ORDER BY `b`.`added` DESC LIMIT 10");
		echo mysql_error();
		while ($data = mysql_fetch_assoc ($sql)) {

			// Nadpis obrázku
			$nadpis = implode (' - ', array_filter (array ($data['nadpis'], $data['popis'])));
			if (empty ($nadpis)) $nadpis = $translate['notitle'];
			
			// Posledný koment
			list ($text) = mysql_fetch_row (mysql_query ("SELECT `text` FROM `{$prefix}_comments` WHERE SUBSTRING(`kde`, 7) = {$data['id']} ORDER BY `added` DESC LIMIT 1"));
			if (mb_strlen ($text) > 32 and false !== mb_strpos ($text, ' ', 32))
			$text = '"' . mb_substr($text, 0, mb_strpos ($text, ' ', 32)) . '..."';
			else $text = '"' . $text . '"';

			// Adresa náhľadu
			$source = 'store/gallery/' . $data['id'] . '.' . $data['type'];
			if ($data['type'] == 'flv') $image = 'media/video.png';
			else if ($data['type'] == 'mp3') $image = 'media/music.png';
			else $image = 'media/image.php?file=' . $source . '&amp;h=135&amp;w=240';

			// Výstup
			$out .= '<div class="pavio_present">'.n;
			$out .= ' ' . $browser -> call ($data['id'], $data['type'], $source, $nadpis).n;
			$out .= '  <img src="' . $image . '" alt="' . $nadpis . '" />' .n;
			$out .= '  <p>' . $text . '</p>'.n;
			$out .= ' </a>' . n . '</div>'.n;
			if (@++$i % 2 == 0) $out .= '<span class="pavio_clean">&nbsp;</span>'.n;
		};
		return $out;
	}
};