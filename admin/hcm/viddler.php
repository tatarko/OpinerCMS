<?php

if (!isset ($param1) or empty ($param1)) return '';

$param1 = explode ('&', $param1);
$param1 = explode ('=', $param1[0]);
if (count ($param1) < 2) return '';
$param1 = $param1[1];
$out = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="437" height="288" id="viddler"><param name="movie" value="http://www.viddler.com/player/' . $param1 . '/" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><embed src="http://www.viddler.com/player/' . $param1 . '/" width="437" height="288" type="application/x-shockwave-flash" allowScriptAccess="always" allowFullScreen="true" name="viddler" ></embed></object>'
?>
