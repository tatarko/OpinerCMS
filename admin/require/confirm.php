<?php
if(!defined("in"))exit();
$out .= '<p class="right">
 <a href="admin.php?what=redactors">' . TempIcon ('cat') . ' ' . $translate['redactors'] . '</a>
 <a href="admin.php?what=massmail">' . TempIcon ('cat') . ' ' . $translate['redactors.massmail'] . '</a>
 <a href="admin.php?what=confirm">' . TempIcon ('cat') . ' ' . $translate['redactors.confirm'] . '</a>
</p>'.n;





/*------ Úvodná tabuľka -----*/

$out = HeadIfPost ($translate['redactors.confirm']);
if (isset ($_GET['id']) and @mysql_query ("UPDATE `{$prefix}_clanky` SET `confirmed` = 1 WHERE `id` = " . adjust ($_GET['id'], true) . " LIMIT 1"))
$out = GetIcon ('info', $translate['successact']);
$out.='<table id="admintable" cellspacing="3px">
	<tr>
		<th width="30px">ID</th>
		<th>' . $translate['title'] . '</th>
		<th width="140px">' . $translate['redactors.redactor'] . '</th>
		<th width="140px">' . $translate['arts.date'] . '</th>
		<th width="75px">' . $translate['action'] . '</th>
	</tr>'.n;
$sql = @mysql_query ("SELECT `a`.`id`, `a`.`nadpis`, `a`.`seo`, `b`.`name`, `a`.`added` FROM `{$prefix}_clanky` as `a`, `{$prefix}_moderators` as `b` WHERE `a`.`confirmed` = 0 AND `a`.`autor` = `b`.`id`");
while ($info = mysql_fetch_assoc ($sql)) {
	$out .= '	<tr>
		<td>' . $info['id'] . '</td>
		<td>' . $info['nadpis'] . '</td>
		<td>' . $info['name'] . '</td>
		<td>' . $info['added'] . '</td>
		<td>
			<a href="admin.php?what=confirm&amp;id=' . $info['id'] . '">' . TempIcon ('accept') . '</a>
			<a href="admin.php?what=articles&amp;mod=delete&amp;id=' . $info['id'] . '">' . TempIcon ('delete') . '</a>
			<a href="' . _SiteLink . rwl ('clanok', $info['id'] . '-' . $info['seo']) . '" target="_blank">' . TempIcon ('blank') . '</a>
		</td>
	</tr>'.n;
};
if (mysql_num_rows ($sql) == 0) $out .= '<tr><td colspan="5">' . $translate['nocontent'] . '</td></tr>'.n;
$out .= '</table>';
?>