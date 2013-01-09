<?php
if (!file_exists ('../admin/includes/default-vars.php')) exit (); include_once ('../admin/includes/default-vars.php');
if (!file_exists ('../media/get-config.php')) exit (); include ('../media/get-config.php');
$info[] = @mysql_fetch_row (@mysql_query ("SELECT UNIX_TIMESTAMP(`kedy`) FROM `{$prefix}_hits` ORDER BY `id` ASC LIMIT 1"));
$info[] = explode ('~$~', SystemInfo);
echo "{$info[1][0]}\n{$info[1][1]}\n{$info[0][0]}\n{$_CONFIG['title']}\n{$_CONFIG['desc']}";
?>