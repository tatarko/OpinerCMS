<?php

class coderunnerapp extends plugin {

	protected function load () {
		$this -> title = 'CodeRunner';
		$this -> version = '1.0';
		$this -> author = 'Ovalio';
		$this -> url = 'http://opiner-cms.net/';
		$this -> description = 'Potrebujete rýchlo spustiť nejaký PHP alebo MySQL kód? Použite túto aplikáciu, ktorá Vám vráti výsledok, prípadne zobrazí chybové hlásenia!';
		$this -> modes = array ('application');
		$this -> cache = false;
		$this -> redactors = false;
		$this -> values = array ();
		$this -> tables = array ();
	}
	
	protected function application () {
		global $prefix, $tohead;
		$out = '';
		if (isset ($_GET['php'])) {
			$out .= '<h2>Výsledok:</h2>'.n;
			$return = '';
			eval ($_GET['php']);
			$out .= nl2br (htmlspecialchars ($return, ENT_QUOTES));
		} else if (isset ($_GET['mysql'])) {
			$out .= '<h2>Výsledok:</h2>'.n;
			if ($query = @mysql_query ($_GET['mysql'])) {
				$out .= getIcon ('info', 'Príkaz bol úspešne vykonaný!');
				if (@mysql_num_rows ($query) > 0) {
					while ($info = mysql_fetch_assoc ($query)) {
						$table[] = $info;
					};
					$tohead[] = '<style type="text/css">#admintable tr td, #admintable tr th{font-size: 0.7em; padding: 1px; border: none;}</style>';
					$out .= '<table id="admintable" cellspacing="3px">'."\n\t<tr>\n";
					foreach ($table[0] as $index => $value) $out .= "\t\t<th>$index</th>\n";
					$out .= "\t</tr>\n";
					foreach ($table as $row) $out .= "\t<tr>\n\t\t<td>" . implode ("</td>\n\t\t<td>", $row) . "\n\t</tr>\n";
					$out .= "</table>\n";
				};
			} else $out .= getIcon ('error', 'Nastala chyba pri spracovaní:') . '<p>'.@mysql_error().'</p>';
		};
		return $out . '<h2>PHP</h2>
<p>Výsledok výstupu priraďujte premennej $return</p>
<form action="' . _SiteForm . '" method="post">
<textarea name="php" rows="10">' . (isset ($_GET['php'])?$_GET['php']:'') . '</textarea><br />
<input type="submit" value="Vykonať" />
</form>
<h2>MySQL</h2>
<p>Prefix u Vaších tabuliek je '.$prefix.'</p>
<form action="' . _SiteForm . '" method="post">
<textarea name="mysql" rows="10">' . (isset ($_GET['mysql'])?$_GET['mysql']:'') . '</textarea><br />
<input type="submit" value="Vykonať" />
</form>';
	}
};