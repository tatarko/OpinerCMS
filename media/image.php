<?php

if (!isset ($_GET['file'])
or file_exists ($_GET['file']))
exit ();
define ('in', true);
include ('../admin/includes/image.class.inc.php');
$_GET['file'] = strpos ($_GET['file'], 'http://') === false ? '../' . $_GET['file'] : $_GET['file'];
$w = isset ($_GET['w']) ? intval ($_GET['w']) : 200;
$h = isset ($_GET['h']) ? intval ($_GET['h']) : 80;
$image = new image ($_GET['file']);
$image -> resize ($w, $h) -> output ();

?>