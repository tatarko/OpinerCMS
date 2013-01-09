<?php

class shadowbox
{

	// Názov prehliadača obrázkov
	public $name = 'Shadowbox';

	// Verzia prehliadača
	public $version = '3.0';

	// Meno autora
	public $author = 'Michael J. I. Jackson';

	// Link na autora
	public $link = 'http://www.mjijackson.com/';

	// Identifikátor galérie
	public $gallery = null;



	
	// Načítanie prehliadača
	public function load ()
	{
		global $tohead;
		$tohead[] = '<link rel="stylesheet" type="text/css" href="codes/shadowbox/shadowbox.css" />';
		$tohead[] = '<script type="text/javascript" src="admin/remote/js/jquery.js"></script>';
		$tohead[] = '<script type="text/javascript" src="codes/shadowbox/shadowbox.js"></script>';
		$tohead[] = '<script type="text/javascript">
  Shadowbox.init({
   language:   "sk",
   players:  ["img", "swf", "flv"],
  });
 </script>';
	}



	// Vykreslenie odkazu na prehliadanie
	public function call ($id, $suffix, $image, $title)
	{
		switch ($suffix) {
			case 'mp3': $i = ';height=0;width=480'; break;
			case 'flv': $i = ';height=480;width=800'; break;
			default: $i = '';
		};
		return '<a href="' . $image . '" title="' . $title . '" rel="shadowbox[' . $this -> gallery . ']' . $i . '">';
	}
};
?>