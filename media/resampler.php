<?php
if (isset ($_GET['i'], $_GET['h'], $_GET['t']) and !empty ($_GET['i']) and !empty ($_GET['h'])) {
	clearstatcache ();
	$name = 'image' . $_GET['i'] . '_' . $_GET['h'] . '.' . $_GET['t'];
	$type = str_replace ('jpg', 'jpeg', $_GET['t']);
	if (!file_exists ('../store/cache/' . $name)) {
		list ($w, $h) = getimagesize ('../store/gallery/' . $_GET['i'] . '.' . $_GET['t']);
		if ($h > $_GET['h'] * 1.1) {
			$nw = $w / ($h / $_GET['h']);
			$nh = $_GET['h'];
			$ip = @imagecreatetruecolor ($nw, $nh);
			eval ('$i = @imagecreatefrom' . $type . ' ("../store/gallery/' . $_GET['i'] . '.' . $_GET['t'] . '");');
			@imagecopyresampled ($ip, $i, 0, 0, 0, 0, $nw, $nh, $w, $h);
			eval ('@image' . $type . ' ($ip, "../store/cache/' . $name . '");');
		} else @file_put_contents ('../store/cache/' . $name, @file_get_contents ('../store/gallery/' . $_GET['i'] . '.' . $_GET['t']));		
	};
	header ('Location: ../store/cache/' . $name);
} else header ('Location: ../index.php');	
?>