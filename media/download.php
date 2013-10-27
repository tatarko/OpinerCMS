<?php
if(!isset($_GET['file'])
		|| $_GET['file'] == ''
		|| !is_numeric($_GET['file'])) {

	exit();
};

require_once 'get-config.php';

if($result = @mysql_fetch_row(@mysql_query(sprintf('SELECT `id`, `file` FROM `%s_download` WHERE `id` = %d LIMIT 1', $prefix, $_GET['file'])))) {

	list($id, $name) = $result;

	@mysql_query(sprintf('UPDATE `%s_download` SET `hits` = `hits` + 1 WHERE `id` = %d LIMIT 1', $prefix, $id));

	Header('Content-Description: File Transfer');
	Header('Content-Type: application/force-download');
	Header('Content-Disposition: attachment; filename="' . substr($name, strrpos($name, '/') + 1) . '"');

	readfile ('../' . $name);
}
?>