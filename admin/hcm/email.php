<?php

$out = '<form action="' . rwl('stranka', 'email') . '" method="post">' . n;
if ($param1 == 0)
	$out .= "\t<strong>Príjemca:</strong><br />\n\t<input type='text' name='who' size='30' /><br />\n";
	else $out .= "\t<input type='hidden' name='who' value='{$_CONFIG['mail']}' />\n";
if ($param2 == 0)
	$out .= "\t<strong>Váš Email:</strong><br />\n\t<input type='text' name='sender' size='30' /><br />\n";
	else $out .= "\t<input type='hidden' name='sender' value='$param2' />\n";
if ($param3 == 0)
	$out .= "\t<strong>Predmet:</strong><br />\n\t<input type='text' name='subject' size='30' /><br />\n";
	else $out .= "\t<input type='hidden' name='subject' value='$param3' />\n";
$out .= "\t<strong>Telo správy:</strong><br />\n\t<textarea name='text' cols='5' cols='30'></textarea><br />\n";
$out .= "<input type='submit' name='Odoslať'>\n";
$out .= "</form>\n";

?>