<?php

class addarticleapp extends plugin {

	protected function load () {
		$this -> title = 'Pridanie článku';
		$this -> version = '1.0';
		$this -> author = 'Ovalio';
		$this -> url = 'http://opiner.tatarko.sk/';
		$this -> description = 'Pridajte si túto aplikáciu medzi widgety a budete tak môcť pridávať články ešte rýchlejšie.';
		$this -> modes = array ('widget');
		$this -> cache = true;
		$this -> redactors = true;
		$this -> values = array ();
		$this -> tables = array ();
	}
	
	protected function widget () {
		global $prefix;
		$out = '<form action="admin.php?what=articles&mod=add" method="post">
	<input type="hidden" name="comments" value="" />
	<input type="hidden" name="imagelink" value="" />
	<input type="hidden" name="actualdt" value="" />
	<input type="hidden" name="category2" value="0" />
	<input type="hidden" name="category3" value="0" />
	<input type="hidden" name="text" value="" />
	<strong>Názov článku:</strong><br />
	<input type="text" name="nadpis" /><br />
	<strong>Kategória</strong><br />
	<select name="category1">'.n;
	$sql = mysql_query ("SELECT `id`, `nadpis` FROM `{$prefix}_cats` ORDER BY `nadpis` ASC");
	while ($tab = mysql_fetch_assoc ($sql)) $out .= '		<option value="' . $tab['id'] . '">' . $tab['nadpis'] . '</option>'.n;
	$out .= '	</select><br />
	<strong>Text článku:</strong><br />
	<textarea name="perex" rows="7"></textarea><br />
	<input type="checkbox" name="showing" checked="checked" id="publication" /> <label for="publication">Publikovať</label>
	<input type="submit" value="Pridať" name="ok" />
</form>';
	return $out;
	}
};