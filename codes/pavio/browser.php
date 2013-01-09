<?php

class pavio
{

	// Názov prehliadača obrázkov
	public $name = 'Pavio';

	// Verzia prehliadača
	public $version = '1.0';

	// Meno autora
	public $author = 'Tomáš Tatarko';

	// Link na autora
	public $link = 'http://tatarko.sk/';

	// Identifikátor galérie
	public $gallery = null;



	
	// Načítanie prehliadača
	public function load ()
	{
		global $tohead;
		$tohead[] = '<link rel="stylesheet" type="text/css" href="codes/pavio/pavio.css" />';
		$tohead[] = '<script type="text/javascript" src="admin/remote/js/jquery.js"></script>';
		$tohead[] = '<script type="text/javascript" src="codes/pavio/pavio.js"></script>';
	}



	// Vykreslenie odkazu na prehliadanie
	public function call ($id, $suffix, $image, $title)
	{
		return '<a href="' . $image . '" title="' . $title . '" rel="pavio' . ((empty ($this -> gallery)) ? '' : '/' . $this -> gallery) . '">';
	}
};
?>