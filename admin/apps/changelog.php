<?php

class changelogapp extends plugin {

	protected function load () {
		$this -> title = 'Changelog';
		$this -> author = 'Ovalio';
		$this -> url = 'http://opiner.tatarko.sk/';
		$this -> description = 'Prehľadný zoznam zmien pri nových verziách redakčného systému Opiner CMS až po jeho počiatky.';
		$this -> modes = array ('application', 'widget');
		$this -> cache = true;
		$this -> redactors = true;
		$this -> values = array (
			'updates'	=> array ('int', 2, 'Počet posledných aktualizácií (widget)'),
			'changes'	=> array ('int', 3, 'Počet zmien (widget)'),
		);
		$this -> tables = array ();
	}

	public function application () {
		if ($xml = @simplexml_load_file ('http://feedback.tatarko.sk/versions.xml')
		and isset ($xml -> version)) {
			foreach ($xml -> version as $values) {
				if (isset ($values -> name, $values -> tspk, $values -> changes, $values -> changes -> change)) {
					@$out .= '<a name="' . $values -> tspk . '"></a>'.n;
					$out .= '<h2>' . $values -> name . '</h2>'.n;
					$out .= '<p><b>TSPK:</b> ' . $values -> tspk;
					if (isset ($values -> from, $values -> link)) {
						if ($values -> from > in) $out .= ' (zatiaľ nie je možné aktualizovať)';
						else if ($values -> from == in) $out .= ' (<a href="' . $values -> link . '" title="Stiahnúť aktualizačný balíček, aby ste mohli Váš systém aktualizovať na túto verziu">stiahnúť aktualizáciu</a>)';
						else $out .= ' (už aktualizované)';
					};
					$out .= '</p>'.n.'<ul>'.n;
					if (is_object ($values -> changes) and is_object ($values -> changes -> change)) {
						foreach ($values -> changes -> change as $value) $out .= '	<li>' . $value . '</li>'.n;
					};
					$out .= '</ul>'.n;
				};
			};
			return (isset ($out)) ? $out : '<p>Žiadna verzia nenájdená!</p>';
		} else return '<p>Pri čítaní externého súboru došlo k chybe!</p>';
	}
	
	

	public function widget () {
		if ($xml = @simplexml_load_file ('http://feedback.tatarko.sk/versions.xml')
		and isset ($xml -> version)) {
			foreach ($xml -> version as $values) {
				if (isset ($values -> name, $values -> tspk, $values -> changes, $values -> changes -> change)) {
					@$out .= '<h3>' . $values -> name . '</h3>'.n;
					if (isset ($values -> from, $values -> link) and $values -> from == in and ADMIN) $out .= '<p><a href="' . $values -> link . '" title="Stiahnúť aktualizačný balíček">stiahnúť aktualizáciu</a></p>'.n;
					$out .= '<ul>'.n;
					if (is_object ($values -> changes) and is_object ($values -> changes -> change)) {
						for ($ii = 1; $ii <= $this -> config['changes'] and isset ($values -> changes -> change[$ii]); ++$ii) $out .= '	<li>' . $values -> changes -> change[$ii] . '</li>'.n;
						$out .= '	<li><a href="admin.php?app=' . $this -> apphash . '#' . $values -> tspk . '">Viac detailov</a></li>'.n;
					};
					$out .= '</ul>'.n;
					if (@++$i == $this -> config ['updates']) break;
				};
			};
			return (isset ($out)) ? $out : '<p>Žiadna verzia nenájdená!</p>';
		} else return '<p>Pri čítaní externého súboru došlo k chybe!</p>';
	}
};