<?php

class coderunnerapp extends plugin {

	protected function load () {
		$this -> title = 'CodeRunner';
		$this -> version = '2.0';
		$this -> author = 'Ovalio';
		$this -> url = 'http://opiner.tatarko.sk/';
		$this -> description = 'Potrebujete rýchlo spustiť nejaký PHP alebo MySQL querinu? Použite túto aplikáciu, ktorá Vám vráti výsledok, prípadne zobrazí chybové hlásenia!';
		$this -> modes = array ('application', 'widget');
		$this -> cache = true;
		$this -> redactors = false;
		$this -> values = array ();
		$this -> tables = array (
			'queries' => '`id` int unsigned NOT NULL AUTO_INCREMENT, `name` tinytext NOT NULL, `query` text NOT NULL, `type` SET("php","sql") NOT NULL DEFAULT "php", PRIMARY KEY (`id`)',
		);
	}
	
	protected function widget () {
		$out='';

		$sql = $this->query('select `id`, `name` from `{prefix}queries` where `type` = "php" order by `name` asc');
		if($sql && mysql_num_rows($sql)) {
			$out .= '<h4>Ulożené kódy</h4><ul>'.PHP_EOL;
			while($row = mysql_fetch_assoc($sql))
			$out.='<li><a href="' . $this->createLink($row['id']).'">'.htmlspecialchars($row['name']).'</a></li>'.PHP_EOL;
			$out.='</ul>'.PHP_EOL;
		}
		
		$sql = $this->query('select `id`, `name` from `{prefix}queries` where `type` = "sql" order by `name` asc');
		if($sql && mysql_num_rows($sql)) {
			$out .= '<h4>Ulożené queriny</h4><ul>'.PHP_EOL;
			while($row = mysql_fetch_assoc($sql))
			$out.='<li><a href="' . $this->createLink($row['id']).'">'.htmlspecialchars($row['name']).'</a></li>'.PHP_EOL;
			$out.='</ul>'.PHP_EOL;
		}
		
		return $out;
	}
	
	protected function application () {
		global $prefix, $tohead;
		$out = '';
		if(isset($_GET['runId']) && $_GET['runId'] && $data = mysql_fetch_assoc($this->query('select `query`, `type` from `{prefix}queries` where `id` = '.intval($_GET['runId']).' limit 1'))) {
			$_GET[$data['type']] = $data['query'];
		}
		if (isset ($_GET['php'])) {
			$out .= '<h2>Výsledok:</h2>'.n;
			$return = '';
			$toReturn = eval ($_GET['php']);
			$out .= '<pre>' . ($toReturn ? $toReturn : $return) . '</pre>';
			if(isset($_GET['save']) && $_GET['save'] == 1) {
				$this->query('insert into `{prefix}queries` (`name`, `query`, `type`) values ("'.adjust($_GET['name']).'", "'.adjust($_GET['php']).'", "php");');
			}
		} else if (isset ($_GET['sql'])) {
			$out .= '<h2>Výsledok:</h2>'.n;
			if ($query = @mysql_query ($_GET['sql'])) {
				$out .= getIcon ('info', 'Príkaz bol úspešne vykonaný!');
				if (@mysql_num_rows ($query) > 0) {
					while ($info = mysql_fetch_assoc ($query)) {
						$table[] = $info;
					};
					$tohead[] = '<style type="text/css">#admintable tr td, #admintable tr th{font-size: 0.7em; padding: 1px; border: none;}</style>';
					$out .= '<table id="admintable" cellspacing="3px">'."\n\t<tr>\n";
					foreach ($table[0] as $index => $value) $out .= "\t\t<th>$index</th>\n";
					$out .= "\t</tr>\n";
					foreach ($table as $row) $out .= "\t<tr>\n\t\t<td>" . implode ("</td>\n\t\t<td>", $row) . "</td>\n\t</tr>\n";
					$out .= "</table>\n";
				};
			} else $out .= getIcon ('error', 'Nastala chyba pri spracovaní:') . '<p>'.@mysql_error().'</p>';
			if(isset($_GET['save']) && $_GET['save'] == 1) {
				$this->query('insert into `{prefix}queries` (`name`, `query`, `type`) values ("'.adjust($_GET['name']).'", "'.adjust($_GET['sql']).'", "sql");');
			}
		};
		$sql1 = $this->query('select `id`, `name` from `{prefix}queries` where `type` = "php" order by `name` asc');
		$sql2 = $this->query('select `id`, `name` from `{prefix}queries` where `type` = "sql" order by `name` asc');

		$out .= '<table cellspacing="15px"><tr><td valign="top"><h2>PHP</h2>
<p>Výsledok výstupu odošlite pomocou <em>return</em></p>
<form method="post" action="'.$this->createLink().'">
<textarea name="php" rows="10">' . (isset ($_GET['php'])?$_GET['php']:'') . '</textarea><br />
<input type="submit" value="Vykonať" />
<input type="checkbox" name="save" value="1" id="savePHP" />
<label for="savePHP">Ulożiť ako</label>
<input type="text" name="name" value="PHP kód" style="width:150px;" />
</form>';
if($sql1 && mysql_num_rows($sql1)) {
	$out .= '<h2>Ulożené kódy</h2><ul>'.PHP_EOL;
	while($row = mysql_fetch_assoc($sql1))
	$out.='<li><a href="' . $this->createLink($row['id']).'">'.htmlspecialchars($row['name']).'</a></li>'.PHP_EOL;
	$out.='</ul>'.PHP_EOL;
}
$out.='</td><td valign="top">
<h2>SQL</h2>
<p>Prefix u Vaších tabuliek je <em>'.$prefix.'</em></p>
<form method="post" action="'.$this->createLink().'">
<textarea name="sql" rows="10">' . (isset ($_GET['sql'])?$_GET['sql']:'') . '</textarea><br />
<input type="submit" value="Vykonať" />
<input type="checkbox" name="save" value="1" id="saveSQL" />
<label for="saveSQL">Ulożiť ako</label>
<input type="text" name="name" value="SQL query" style="width:150px;" />
</form>';
if($sql2 && mysql_num_rows($sql2)) {
	$out .= '<h2>Ulożené queriny</h2><ul>'.PHP_EOL;
	while($row = mysql_fetch_assoc($sql2))
	$out.='<li><a href="' . $this->createLink($row['id']).'">'.htmlspecialchars($row['name']).'</a></li>'.PHP_EOL;
	$out.='</ul>'.PHP_EOL;
}
$out.='</td></tr></table>';
	return $out;
	}

	protected function createLink($id = null){
		return 'admin.php?app='.$this->apphash.($id ? '&amp;runId='.$id : '');
	}
};