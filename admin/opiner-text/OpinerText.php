<?php





/*----- Trieda OpinerText -----*/

class OpinerText {



	/*--- Premenné Triedy ---*/

	private $file;				// Adresa k súboru, ktorý editujeme
	private $text;				// Obsah súboru
	private $localdir;			// Adresa k súborom triedy
	private $config = array (		// Defaultná konfigurácia
		'toolbar'	=> 'default',	// Lišta nástrojov
	);



	/*--- Konštruktor Triedy ---*/

	public function __construct ($file, $link, $without = false) {
		if ($without === true) {
			$this->file = $file;
			$this->text = '';
			$this->localdir = dirname (__FILE__) . '/';
			$this->FormLink = $link;
			return true;
		} else {
			if (@file_exists ($file) and false !== ($text = @file_get_contents ($file))) {
				$this->file = $file;
				$this->text = $text;
				$this->localdir = dirname (__FILE__) . '/';
				$this->FormLink = $link;
				return true;
			} else return false;
		};
	}



	/*--- Vykreslenie editora ---*/

	public function __ToString () {
		global $translate;
		$ToolBar = $this->GetToolBarHtml ("\t\t\t", "\n");
		$out = '<form action="' . $this->FormLink . '" method="post" name="OpinerTextForm" OnSubmit="return OpinerExitForm();">' . "\n";
		$out .= "\t<div id=\"opiner-text\">\n";
		$out .= "\t\t<div id=\"opiner-text-toolbar\" name=\"OPINERTOOLBAR\">\n";
		$out .= $ToolBar . "<br />\n";
		$out .=	"\t\t\t{$translate['fman.edfile']}: <kbd>" . str_replace ('./', '', $this->file) . "<kbd>\n";
		$out .= "\t\t</div>\n\t\t<center>\n";
		$out .= "\t\t\t<textarea id=\"opiner-text-area\" name=\"text\" rows=\"50\">" . $this->TextFilter () . "</textarea>\n";
		$out .= "\t\t</center>\n\t</div>\n";
		return $out;
	}



	/*--- Filtrovanie textu ---*/

	private function TextFilter () {
		return str_replace (array ('<', '>'), array ('&lt;', '&gt;'), $this->text);
	}



	/*--- Vzatie Hodnoty Konfigurácie ---*/

	public function GetConfig ($what) {
		if (isset ($this->config[$what]))
		return $this->config[$what];
		else return false;
	}



	/*--- Nastavenie Hodnoty Konfigurácie ---*/

	public function SetConfig ($what, $value) {
		if (isset ($this->config[$what])) {
			$this->config[$what] = $value;
			return true;
		} else return false;
	}



	/*--- Vrátenie Lišty (HTML) ---*/

	private function GetToolbarHtml ($NewLine, $LineSepparator) {
		$this->SetConfig ('toolbar', $this->GetToolbar ());
		if (!is_array ($this->config['toolbar'])) return false;
		foreach ($this->config['toolbar'] as $value => $array) {
			if (isset ($array['title'], $array['onclick']))
			$_RETURN[] = $NewLine . '<input type="image" name="ok" value="' . $value . '" src="admin/opiner-text/images/icons/' . $value . '.png" title="' . $array['title'] . '" onclick="' . $array['onclick'] . '" />';
		};
		if (isset ($_RETURN) and is_array ($_RETURN)) {
			return implode ($LineSepparator, $_RETURN);
		} else return false;
	}



	/*--- Generovanie Lišty ---*/

	private function GetToolbar () {
		global $translate;
		$where = str_replace ('admin.php', '', $_SERVER['PHP_SELF']);
		$file = $where . substr ($this->file, 2);
		if (is_string ($this->config['toolbar'])) {
			switch ($this->config['toolbar']) {
				default:
					$_TOOLBAR = array (
						'save' => array (
							'title'		=> $translate['save'],
							'onclick'	=> 'return true;',
						),
						'save-as' => array (
							'title'		=> $translate['fman.saveas'],
							'onclick'	=> 'return GetNewFileName ();',
						),
						'view-on-page' => array (
							'title'		=> $translate['fman.preview'],
							'onclick'	=> 'return GoToFile (\'' . $file . '\');',
						),
						'reload' => array (
							'title'		=> $translate['fman.reload'],
							'onclick'	=> 'return ReloadArea ();',
						),
						'exit' => array (
							'title'		=> $translate['fman.cedit'],
							'onclick'	=> 'return true;',
						),
					);
				break;
			};
		} else $_TOOLBAR = $this->config['toolbar'];
		foreach ($_TOOLBAR as $index => $value) {
			if (array_search ($index, array (
				'save',
				'save-as',
				'view-on-page',
				'reload',
				'exit',
			)) !== false and isset ($value['title'], $value['onclick'])) $_RETURN[$index] = $value;
		};
		if (isset ($_RETURN) and is_array ($_RETURN)) return $_RETURN;
		else return false;
	}



	/*--- Nastavenie Textu ---*/

	public function SetText ($text) {
		$this->text = $text;
		return true;
	}



	/*--- Uloženie Súboru ---*/

	public function Save () {
		if (@file_put_contents ($this->file, $this->text)) return true;
		else return false;
	}
};
?>