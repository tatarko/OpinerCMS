<?php

if (!isset ($param1) or empty ($param1)) return '';
$p1 = explode ('uname=', $param1);
if (count ($param1) < 1) return '';
$p2 = explode ('&amp;aid=', $p1[1]);
$p3 = explode ('&amp;continue=', $p2[1]);
$par1 = $p2[0]; $par2 = $p3[0];
if (!isset ($param2)) $param2 = '';
switch ($param2) {
	case 'small': $out = '<embed type="application/x-shockwave-flash" src="http://picasaweb.google.com/s/c/bin/slideshow.swf" width="320" height="240" flashvars="host=picasaweb.google.com&hl=sk&feat=flashalbum&RGB=0x000000&feed=http%3A%2F%2Fpicasaweb.google.com%2Fdata%2Ffeed%2Fapi%2Fuser%2F' . $par1 . '%2Falbumid%2F' .$par2 .'%3Falt%3Drss%26kind%3Dphoto%26hl%3Dsk" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>'; break;
	case 'large': $out = '<embed type="application/x-shockwave-flash" src="http://picasaweb.google.com/s/c/bin/slideshow.swf" width="640" height="480" flashvars="host=picasaweb.google.com&hl=sk&feat=flashalbum&RGB=0x000000&feed=http%3A%2F%2Fpicasaweb.google.com%2Fdata%2Ffeed%2Fapi%2Fuser%2F' . $par1 . '%2Falbumid%2F' .$par2 .'%3Falt%3Drss%26kind%3Dphoto%26hl%3Dsk" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>'; break;
	case 'xlarge': $out = '<embed type="application/x-shockwave-flash" src="http://picasaweb.google.com/s/c/bin/slideshow.swf" width="800" height="600" flashvars="host=picasaweb.google.com&hl=sk&feat=flashalbum&RGB=0x000000&feed=http%3A%2F%2Fpicasaweb.google.com%2Fdata%2Ffeed%2Fapi%2Fuser%2F' . $par1 . '%2Falbumid%2F' .$par2 .'%3Falt%3Drss%26kind%3Dphoto%26hl%3Dsk" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>'; break;
  default: $out = '<embed type="application/x-shockwave-flash" src="http://picasaweb.google.com/s/c/bin/slideshow.swf" width="400" height="300" flashvars="host=picasaweb.google.com&hl=sk&feat=flashalbum&RGB=0x000000&feed=http%3A%2F%2Fpicasaweb.google.com%2Fdata%2Ffeed%2Fapi%2Fuser%2F' . $par1 . '%2Falbumid%2F' .$par2 .'%3Falt%3Drss%26kind%3Dphoto%26hl%3Dsk" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>'; break;
}; 

?>