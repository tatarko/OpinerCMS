<?php

// Kontrola jadra
if (!defined ('in')
or false !== strpos ($_SERVER['PHP_SELF'], '.inc.php'))
die (header ('HTTP/1.1 403 Forbidden') . 'Unauthorized Access!');



// Trieda template
class image
{

	// string Adresa obrázka
	private $filename;

	// string Šírka obrázka
	private $width;

	// string Výška obrázka
	private $height;

	// array MIME typ obrázka
	private $type;

	// string Prípona názvu obrázka
	private $suffix;

	// imagegd Kreslacie plátno imagegd knižnice
	private $palette;

	// int constant Biela farba
	private static $white = 16777215;

	// int constant Čierna farba
	private static $black = 0;



	/**
	 *	Vytvorenie objektu, určenie základných premenných
	 *	@param string name Fyzická adresa obrázka
	 *	@return object self
	 */

	public function __construct ($filename)
	{
	        if (file_exists ($filename))
	        {
	                $this -> filename = $filename;
			$info = getimagesize ($this -> filename);
			$this -> width = $info[0];
			$this -> height = $info[1];
			$this -> type = $info['mime'];
			$this -> suffix = strtolower (substr ($this -> filename, strrpos ($this -> filename, '.') + 1));
			switch ($this -> suffix)
			{
				case 'jpg': $this -> palette = imagecreatefromjpeg ($this -> filename); break;
				case 'png': $this -> palette = imagecreatefrompng ($this -> filename); break;
			};
			return $this;
		} else return false;
	}



	/**
	 *      Zmenšenie na presné zadané rozmery, pokiaľ pôvodný
	 *      obrázok nemá taký pomer šírky a výšky, bude orezaný
	 *      @param int width Šírka zmenšeného obrázka
	 *      @param int height Výška zmenšeného obrázka
	 *      @param string filename Kam uložiť zmenšený obrázok
	 *      @param boolean filename Default hodnota false, obrázok sa zmenší iba v rámci objektu
	 *      @return object self
	 */

	public function resize ($width, $height, $filename = false)
	{
	        $ratio_orig = $this -> width / $this -> height;
	        $ratio_new = $width / $height;
		if ($ratio_orig > $ratio_new)
		{
			$help = ceil ($this -> width / ($this -> height / $height));
			$helppalette = imagecreatetruecolor ($help, $height);
			imagefilledrectangle ($helppalette, 0, 0, $help, $height, image::$white);
			imagecopyresampled ($helppalette, $this -> palette, 0, 0, 0, 0, $help, $height, $this -> width, $this -> height);
			$palette = imagecreatetruecolor ($width, $height);
			imagecopy ($palette, $helppalette, 0, 0, round (($help - $width)/2), 0, $width, $height);
		}
		else if ($ratio_orig < $ratio_new)
		{
			$help = ceil ($this -> height / ($this -> width / $width));
			$helppalette = imagecreatetruecolor ($width, $help);
			imagefilledrectangle ($helppalette, 0, 0, $width, $help, image::$white);
			imagecopyresampled ($helppalette, $this -> palette, 0, 0, 0, 0, $width, $help, $this -> width, $this -> height);
			$palette = imagecreatetruecolor ($width, $height);
			imagecopy ($palette, $helppalette, 0, 0, 0, round (($help - $height)/2), $width, $height);
		}
		else if ($ratio_orig == $ratio_new)
		{
			$palette = imagecreatetruecolor ($width, $height);
			imagecopyresampled ($palette, $this -> palette, 0, 0, 0, 0, $width, $height, $this -> width, $this -> height);
		}
		if (empty ($filename))
		{
			$this -> palette = $palette;
			$this -> width = $width;
			$this -> height = $height;
			return $this;
		}
		else return $this -> output ($palette, $filename);
	}



	/**
	 *      Výsledné vygenerovanie obrázka z kreslacieho plátna
	 *      @param string filaname Kam uložiť obrázok
	 *      @param boolean false filename Ak má byť obrázok vykreslený do prehliadača
	 *      @param int q Kvalita ukladaného JPG obrázku
	 *      @return object self
	 */

	public function output ($filename = false, $q = 90)
	{
		if ($filename === false)
		Header ('Content-type: ' . $this -> type);
		switch ($this -> suffix)
		{
			case 'jpg': imagejpeg ($this -> palette, $filename, 90);
			case 'png': imagepng ($this -> palette, $filename);
		}
		return $this;
	}
}
?>