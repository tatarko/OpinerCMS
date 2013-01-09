<?php
if (!isset ($_GET['file']) or $_GET['file'] == '') exit ();
include ('get-config.php');
list ($name) = @mysql_fetch_row (@mysql_query ("SELECT `file` FROM `{$prefix}_download` WHERE `id` = '{$_GET["file"]}' LIMIT 1"));
mysql_query ("UPDATE `{$prefix}_download` SET `hits` = `hits` + 1 WHERE `id` = '{$_GET["file"]}' LIMIT 1");
Header ('Content-Description: File Transfer');
Header ('Content-Type: application/force-download');
Header ('Content-Disposition: attachment; filename="' . substr ($name, strrpos ($name, '/') + 1) . '"');
readfile ('../' . $name);
?>