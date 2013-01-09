<?php
if (!defined ('in')) exit ();

$out = HeadIfPost ($translate['apps']);

		$out .= '
		<table id="homepage" cellspacing=7px>
			<tr>'.n;

		$sql = @mysql_query ("SELECT SHA1(CONCAT(`id`, `fname`, `installed`)) as `hash`, `title`, `description` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `application` = 1 AND `homepage` = 1" . ((ADMIN)?'':' AND `redactors` = 1') . " ORDER BY `title` ASC");
		while ($info = @mysql_fetch_assoc ($sql)) $_PANELS[] = array ('app=' . $info['hash'], $info['title'], $info['description']);

		foreach ($_PANELS as $i => $panel) {
		$out .= '		<td>
					<h3><a href="admin.php?' . $panel[0] . '">' . $panel[1] . '</a>' . ((isset ($panel[3]))?' <a href="admin.php?' . $panel[0] . $panel[3] . '"><span>' . $translate['add'] . '</span></a>':'') . '</h3>
					<p>' . $panel[2] . '</p>
				</td>'.n;
			if (++$i % 4 == 0) $out .= "\t</tr>\n\t<tr>\n";
		};
		$out .= '	</tr>
		</table>'.n;
?>