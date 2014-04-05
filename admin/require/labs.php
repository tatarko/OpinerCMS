<?php
if (!defined ('in')) exit ();
$out = HeadIfPost ($translate['labsprojects']);
$out .= '<p class="right">
 <a href="?what=file-manager">' . TempIcon ('cat') . ' ' . $translate['fman'] . '</a>
 <a href="?what=file-manager&mod=download-manager">' . TempIcon ('cat') . ' ' . $translate['fman.down'] . '</a>
 <a href="?what=statistics">' . TempIcon ('cat') . ' ' . $translate['stats'] . '</a>
 <a href="?what=backup">' . TempIcon ('cat') . ' ' . $translate['backup'] . '</a>
 <a href="?what=labs">' . TempIcon ('cat') . ' ' . $translate['labsprojects'] . '</a>
</p>'.n;
if (isset ($_REQUEST['posted'])) {
	$iok = true;
	foreach(System::app()->labsProjects as $index => $value) {
		$tosave = isset ($_REQUEST['labs' . $value]) ? 1 : 0;
		if (false === (@mysql_fetch_row (@mysql_query ("SELECT `nazov` FROM `{$prefix}_config` WHERE `nazov` = 'labs.$index' LIMIT 1"))))
		$iok = @mysql_query ("INSERT INTO `{$prefix}_config` VALUES('labs.$value', $tosave);") and $iok ? true : false;
		else $iok = ConfigUpdate ('labs.' . $value, $tosave) and $iok ? true : false;
	};
	$out = $iok ? getIcon ('info', $translate['successact']) : getIcon ('info', $translate['failureact']);
};
$out .= '<form action="admin.php?what=' . $what . '" method="post">
 <fieldset>'.n;
foreach (System::app()->labsProjects as $index => $value) {
	$out .= '  <input type="checkbox" name="labs' . $value . '" value="1" id="' . $index . '"' . ((isset ($_CONFIG['labs.' . $value]) and $_CONFIG['labs.' . $value] == 1) ? ' checked="checked"' : '') . ' /> <label for="' . $index . '">' . $translate['labs.' . $index] . '</label><br />'.n;
};
$out .= ' </fieldset>
<input type="submit" name="posted" value="' . $translate['submit'] . '" />
</form>';
$oit 
?>