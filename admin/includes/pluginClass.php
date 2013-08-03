<?php

function loadPlugin ($name, $type = 'load') {
	if (!class_exists ($name . 'app')) {
		if (@file_exists ('admin/apps/' . $name . '.php')) {
			include ('admin/apps/' . $name . '.php');
			if (!class_exists ($name . 'app')) return false;
		} else return false;
	};
	eval ('$plugin = new ' . $name . 'app ("' . $name . '", "' . $type . '");');
	if ($plugin -> checkRun ())
	return $plugin;
	else return false;
};

class plugin {

	public $title = 'Application';
	public $author = 'Unknown';
	public $url = 'http://opiner.tatarko.sk/';
	public $description = '(without description)';
	public $version = '1.0';
	protected $name;
	protected $mode;
	protected $config = array ();
	protected $modes = array ();
	protected $cache = true;
	protected $redactors = true;
	protected $values = array ();
	protected $tables = array ();



	public function __construct ($name, $mode = 'load') {
		$this -> name = $name;
		$this -> mode = $mode;
		$this -> load ();
	}



	public function checkRun () {
		global $prefix;
		$this -> modes[] = 'load';
		if (array_search ($this -> mode, $this -> modes) !== false and method_exists ($this, $this -> mode)) {
			list ($this -> apphash) = mysql_fetch_row (mysql_query ("SELECT SHA1(CONCAT(`id`, `fname`, `installed`)) FROM `{$prefix}_apps` WHERE `fname` = '" . adjust ($this -> name) . "' LIMIT 1"));
			if ($this -> mode != 'load' and !empty ($this -> values)) $this -> loadConf ();
			return true;
		} else return false;
	}
	
	
	public function canRun ($mode) {
		if (array_search ($mode, $this -> modes) !== false) return true;
		else return false;
	}



	public function run () {
		if (!empty ($this -> values) or !empty ($this -> tables)) $this -> runDB ();
		eval ('$return = $this -> ' . $this -> mode . ' ();');
		return $return;
	}
	
	
	
	public function runDB () {
		foreach ($this -> values as $index => $values) {
			if (!isset ($this -> config [$index]))
			$this -> addSetting ($index, $values[1]);
		};
		foreach ($this -> tables as $table => $structure) {
			global $prefix;
			@mysql_query ("CREATE TABLE IF NOT EXISTS `{$prefix}_app{$this->name}_$table` ($structure) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		};
		return true;
	}



	public function uninstall () {
		$isok = true;
		global $prefix;
		foreach ($this -> values as $index => $values) $isok = ($this -> dropSetting ($index) and $isok) ? true : false;
		foreach ($this -> tables as $table => $structure) $isok = (@mysql_query ("DROP TABLE IF EXISTS `{$prefix}_app{$this->name}_$table`") and $isok) ? true : false;
		return $isok;
	}



	protected function loadConf () {
		global $prefix;
		$l = strlen ("plugin_{$this->name}_") + 1;
		$q = @mysql_query ("SELECT SUBSTR(`nazov`, $l) as `nazov`, `hodnota` FROM `{$prefix}_config` WHERE `nazov` LIKE 'plugin_{$this->name}_%'");
		while ($r = @mysql_fetch_array ($q)) $this -> config [$r ['nazov']] = $r ['hodnota'];
	}



	public function name () {
		return $this -> name;
	}



	protected function setSetting ($name, $value) {
		global $prefix;
		if (@mysql_query ("UPDATE `{$prefix}_config` SET `hodnota` = '" . adjust ($value) . "' WHERE `nazov` = 'plugin_" . $this -> name . "_$name' LIMIT 1")
		and $this -> config [$name] = $value)
		return true;
		else return false;
	}



	protected function addSetting ($name, $value) {
		global $prefix;
		if (@mysql_query ("INSERT `{$prefix}_config` VALUES ('plugin_" . $this -> name . "_$name', '" . adjust ($value) . "');")
		and $this -> config [$name] = $value)
		return true;
		else return false;
	}



	protected function dropSetting ($name) {
		global $prefix;
		if (@mysql_query ("DELETE FROM `{$prefix}_config` WHERE `nazov` = 'plugin_" . $this -> name . "_$name' LIMIT 1"))
		return true;
		else return false;
	}



	public function options () {
		if (empty ($this -> values)) return;
		$this -> loadConf ();
		$return = '';
		foreach ($this -> values as $index => $value) {
			switch ($value[0]) {
			
				// Dĺhší text (textarea)
				case 'text': $return .= '<strong>' . $value[2] . '</strong>
		<textarea name="setting[' . $index . ']">' . $this -> config [$index] . '</textarea>'.n;
				break;

				// Číselká
				case 'int': $return .= '<strong>' . $value[2] . '</strong>
		<input type="text" name="setting[' . $index . ']" value="' . adjust ($this -> config [$index], true) . '" />'.n;
				break;

				// Normálny text
				case 'string': $return .= '<strong>' . $value[2] . '</strong>
		<input type="text" name="setting[' . $index . ']" value="' . setQ ($this -> config [$index]) . '" />'.n;
				break;

				// Boolen hodnoty
				case 'boolean':
				$return .= '<input type="checkbox" name="setting[' . $index . ']" ' . (($this -> config [$index] == 1) ? ' checked="checked"' : '') . ' id="button_' . $index . '" value="1" /> <label for="button_' . $index . '">' . $value[2] . '</label>'.n;
				break;
			};
		};
		return $return;
	}



	public function saveOptions ($settings) {
		$isok = true;
		foreach ($settings as $index => $value)
		$isok = ($isok and $this -> setSetting ($index, $value) and $this -> config [$index] = $value) ? true : false;
		return $isok;
	}



	public function hasSettings () {
		return (empty ($this -> values)) ? false : true;
	}



	protected function query ($query) {
		global $prefix;
		$query = str_replace ('{prefix}', "{$prefix}_app{$this->name}_", $query);
		return @mysql_query ($query);
	}



	public function onlyAdmin () {
		return !$this -> redactors;
	}



	public function hasCache () {
		return ($this -> cache) ? 1 : 0;
	}
};
?>