<?php
if (!defined ('in')) define ('in', true);
if (file_exists ('_config.php')) {include ('_config.php');}
else if (file_exists ('../_config.php')) {include ('../_config.php');}
else if (file_exists ('../../_config.php')) {include ('../../_config.php');}
else exit('System configuration file _config.php not found!');
if (@mysql_pconnect ($connect['server'], $connect['user'], $connect['pass']));
else if (@mysql_connect ($connect['server'], $connect['user'], $connect['pass']));
else exit ('Conection to MySQL server failed!');
if (!@mysql_select_db ($connect['dbname'])) exit ('Connection to database has failed');
if ($connType = @mysql_fetch_row (@mysql_query ("SELECT `hodnota` FROM `{$prefix}_config` WHERE `nazov` = 'conntype' LIMIT 1"))
and $connType[0] == 1) @mysql_query ("SET NAMES `utf8` COLLATE `utf8_general_ci`");
$query = @mysql_query ("SELECT * FROM `{$prefix}_config`");
while ($value = @mysql_fetch_row ($query)) $_CONFIG[$value[0]] = str_replace ('\"', '"', $value[1]);
?>