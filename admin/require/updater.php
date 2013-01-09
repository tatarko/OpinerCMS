<?php

if (!defined ('in')) exit ();

if (@file_exists ('updater.php')) {
	include ('updater.php');
	if (isset ($queries, $UpdateTSPK) and is_array ($queries) and count ($queries) > 0
	and (!$info = @mysql_fetch_row (@mysql_query ("SELECT hodnota FROM {$prefix}_config WHERE nazov = 'last-update' LIMIT 1")) or $info[0] > in)) {
		$out .= '<h1 align="center">' . $translate['updater'] . '</h1>'.n;
		foreach ($queries as $query) {
			if (!@mysql_query ($query)) $ERs[] = @mysql_error ();
		};
		if (isset ($ERs)) {
			$out .= '<p style="color:red;font-weight:bold;">' . $translate['updater.errors'] . '</p>';
			foreach ($ERs as $index => $value) $ERs[$index] = '<p>'.$value.'</p>';
			$out .= implode (n, $ERs);
		} else {
			$out .= '<p style="color:green;font-weight:bold;">' . $translate['updater.success'] . '</p>';
			if (!@unlink ('updater.php')) $out .= getIcon ('warning', langrep ('updater.warning', '/updater.php'));
			ConfigUpdate ('last-update', $UpdateTSPK);
		};
	} else Header ('Location: admin.php');
} else Header ('Location: admin.php');
?>