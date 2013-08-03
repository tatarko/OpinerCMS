<?php

class microblogapp extends plugin {

	protected function load () {
		global $_CONFIG;
		$this -> title = $_CONFIG['microblog_head'];
		$this -> version = '1.0';
		$this -> author = 'Ovalio';
		$this -> url = 'http://opiner.tatarko.sk/';
		$this -> description = 'Rýchle správy informojúce Vašich užívateľov o novinkách všeho druhu.';
		$this -> modes = array ('widget');
		$this -> cache = true;
		$this -> redactors = true;
		$this -> values = array ();
		$this -> tables = array ();
	}


	public function widget () {
		global $tohead, $_CONFIG, $prefix;
		$out = '<form action="admin.php?what=microblog&amp;mod=add" method="post">
	<textarea name="blogpost" rows="3"></textarea>
	<input type="submit" value="Pridať nový post" />
</form>'.n;
		return $out;
	}
};