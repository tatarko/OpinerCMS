<?php

if (!isset ($param1) or empty ($param1)) return '';
$param1 = explode ('?v=', $param1);
if (count ($param1) < 2) return '';
$param1 = explode ('&', $param1[1]);
if (strlen ($param1[0]) != 11) return '';
if (!isset ($param2)) $param2 = '';
$param1 = $param1[0];
switch ($param2) {
	case 'small': $out = '<object width="320" height="265"><param name="movie" value="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="320" height="265"></embed></object>'; break;
	case 'large': $out = '<object width="480" height="385"><param name="movie" value="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object>'; break;
	case 'xlarge': $out = '<object width="640" height="505"><param name="movie" value="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="640" height="505"></embed></object>'; break;
	default: $out = '<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $param1 . '&hl=cs&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>'; break;
}; 

?>