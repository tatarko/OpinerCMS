<?php
if (isset ($_GET['id']) and $_GET['id'] != '') {
	clearstatcache ();
	if (!file_exists ('../store/cache/gravatar_' . $_GET['id'] . '.jpg')) {
		file_put_contents ('../store/cache/gravatar_' . $_GET['id'] . '.jpg', file_get_contents ('http://www.gravatar.com/avatar/' . $_GET['id'] . '?s=64&d=http%3A%2F%2Ffeedback.tatarko.sk%2FnoGravatar.jpg'));
	} else {
		$ctime = filectime ('../store/cache/gravatar_' . $_GET['id'] . '.jpg');
		$ctime = date ("d:m:y:H", $ctime);
		$atime = date ("d:m:y:H");
		$ctime = explode (':', $ctime);
		$atime = explode (':', $atime);
		if (!($atime[0] == $ctime[0] and $atime[1] == $ctime[1] and $atime[2] == $ctime[2] and ($atime[3] - 6) < $ctime[3]))
		file_put_contents ('../store/cache/gravatar_' . $_GET['id'] . '.jpg', file_get_contents ('http://www.gravatar.com/avatar/' . $_GET['id'] . '?s=64&d=http%3A%2F%2Ffeedback.tatarko.sk%2FnoGravatar.jpg'));
	};
	header ('Location: ../store/cache/gravatar_' . $_GET['id'] . '.jpg');
};	
?>