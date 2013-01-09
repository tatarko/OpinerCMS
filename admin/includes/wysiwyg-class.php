<?php
if (!defined ('in')) exit ();

class wdriver {

	// Premenne Triedy
	public $name;	// Názov poľa na odoslanie
	public $text;	// Text, ktorý upravujeme
	public $wys;	// Názov Wysiwyg editoru
	public $dir;	// Cesta k súborom wysiwyg dokumentu
	public $file;	// Adresa k súboru, ktorý ovláda wysiwyg

	// Konštruktor
	public function __construct ($name, $text = '') {
		global $_CONFIG;
		$this->name = $name;
		$this->text = $text;
		$this->wys = $_CONFIG['wysiwyg'];
		$this->dir = 'codes/' . $this->wys . '/';
		if (file_exists ($this->dir . $this->wys . '-control.php')) {
			$this->file = $this->dir . $this->wys . '-control.php';
			return true;
		} else if (file_exists ($this->dir . $this->wys . '.php')) {
			$this->file = $this->dir . $this->wys . '.php';
			return true;
		} else return false;
	}

	// Vykreslenie Wysiwyg Prostredia
	public function Draw() {
		global $_CONFIG;
		$desing_file = 'templates/' . $_CONFIG['template'] . '/';
		if ($this->name != '') {
			global $tohead;
			include ($this->file);
			return $return;
		} else return '';
	}
};
 
?>