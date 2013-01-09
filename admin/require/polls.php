<?php
if (!defined ('in')) exit ();
if (!isset ($_REQUEST['mod']) or $_REQUEST['mod'] == '') $_REQUEST['mod'] = 'home';
$out .= '<p class="right">';
if ($_USER_INFO['articles']) $out .= '
 <a href="admin.php?what=articles">' . TempIcon ('cat') . ' ' . $translate['articles'] . '</a>';
if ($_USER_INFO['albums']) $out .= '
 <a href="admin.php?what=gallery">' . TempIcon ('cat') . ' ' . $translate['albums'] . '</a>'; $out .='
 <a href="admin.php?what=polls">' . TempIcon ('cat') . ' ' . $translate['polls'] . '</a>
 <a href="admin.php?what=polls&amp;mod=add">' . TempIcon ('add') . ' ' . $translate['polls.add'] . '</a>
</p>'.n;
switch ($_REQUEST['mod']) {





/*--- Správa ankiet ---*/

case 'home':
	$out = HeadIfPost ($translate['polls']);
	$sql = @mysql_query("SELECT id, question, locked FROM {$prefix}_polls WHERE 1=1".FILTER." ORDER BY id DESC LIMIT ".(($pag-1)*$limit).", $limit");
	$out.='<table id="admintable" cellspacing="3px">
	<tr><th width="30px">ID</th><th>' . $translate['polls.question'] . '</th><th width="50px">' . $translate['action'] . '</th></tr>'.n;
	while ($tab = @mysql_fetch_row ($sql)) {
		@++$ii;
		$out .= '<tr><td>'.$tab[0].'</td><td>';
		$out .= ($tab[2] == 0) ? $tab[1] : '<i>'.$tab[1].'</i>';
		$out .= '</td><td><a href="?what=polls&mod=edit&id='.$tab[0].'">'.TempIcon('edit').'</a><a href="?what=polls&mod=delete&id='.$tab[0].'">'.TempIcon('delete').'</a></td></tr>'.n;
	};
	if (!isset ($ii)) $out .= '<tr><td colspan="3">' . $translate['nocontent'] . '</td></tr>'.n;
	$out .= '</table>'.n;
	$out .= GetPagesList ('polls', 'addons&mod=polls');
break;





/*--- Pridanie ankiet ---*/

case 'add':
	$out = HeadIfPost ($translate['polls.add']);
	if (isset ($_REQUEST['ok'])) {
		if (isset ($_REQUEST['question'], $_REQUEST['ans'])) {
			$INT1 = (isset ($_REQUEST['locked'])) ? 1 : 0;
			unset ($ans);
			foreach ($_REQUEST['ans'] as $index => $value) {
				if (!empty ($value)) {
					$ans[] = adjust ($value);
					$vts[] = adjust ($_REQUEST['vts'][$index], true);
				};
			};
			$votes = implode ('#', $give);
			if (mysql_query ("INSERT INTO {$prefix}_polls VALUES (0, ".USER.", '".adjust($_REQUEST["question"])."', '".implode('#',$ans)."', '".implode('#',$vts)."', $INT1)")) {
				$IID = mysql_insert_id ();
				Header ('Location: admin.php?what=polls&mod=edit&id='.$IID);
			} else $out .= GetIcon ('error', $translate['failureadd']);
		} else $out .= GetIcon ('error', $translate['nofill']);
	};
	$tohead[] = '<script type="text/javascript">
	function addAns () {
		$("#at").append("<input type=\'text\' name=\'ans[]\' /> <input type=\'text\' name=\'vts[]\' style=\'width:30px;\' value=\'0\' /><br />");
		return false;
	}
</script>';
	$out .= '<form action="admin.php?' . $_SERVER["QUERY_STRING"] . '" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['polls.question'] . '</strong>
		<input type="text" name="question" size="45" style="margin:3px;" />
		<strong>' . $translate['polls.answers'] . '</strong>
		<div id="at">'.n;
		for ($i = 1; $i <= 3; ++$i) $out .= '			<input type="text" name="ans[]" /> <input type="text" name="vts[]" style="width:30px;" value="0" /><br />'.n;
		$out .= '		</div>
		<a href="#" onclick="return addAns();">' . $translate['polls.addmore'] . '</a>
		<strong>' . $translate['settings'] . '</strong>
		<input type="checkbox" name="locked" /> ' . $translate['polls.block'] . '
	</fieldset>
	<input type="submit" name="ok" value="' . $translate['add'] . '" />
</form>';
break;





/*--- Úprava ankety ---*/

case 'edit':
	$out = HeadIfPost ($translate['polls.edit']);
	if (isset ($_REQUEST['ok'], $_REQUEST['question'])) {
		$INT1 = (isset ($_REQUEST['locked'])) ? 1 : 0;
		if (isset ($_REQUEST['clear_votes'])) {
			$CV = (mysql_query ("DELETE FROM {$prefix}_iplog WHERE what = 'poll-".adjust($_REQUEST['id'],true)."'")) ? true : false;
			foreach ($_REQUEST['poll-votes'] as $value) $votes[] = '0';
			$votes = implode ('#', $votes);
		} else {
			foreach ($_REQUEST['poll-votes'] as $value) $votes[] = adjust($value,true);
			$votes = implode ('#', $votes);
			$CV = true;
		};
		foreach ($_REQUEST['poll-answers'] as $value) $ans[] = "$value";
		$ans = implode ('#', $ans);
		if (mysql_query ("UPDATE {$prefix}_polls SET question = '".adjust($_REQUEST["question"])."', answers = '$ans', votes = '$votes', locked = $INT1 WHERE id = '".adjust($_REQUEST['id'])."' LIMIT 1;") and $CV)
		$out .= GetIcon ('info', $translate['successact']);
		else $out .= GetIcon ('error', $translate['failureact']);
		
	};
	if (false === ($info = mysql_fetch_row (mysql_query ("SELECT question, answers, votes, locked FROM {$prefix}_polls WHERE id = '".adjust($_REQUEST['id'])."'".FILTER)))) {
		Header ('Location: admin.php?what=addons&mod=polls');
	}; 
	$out .= '<form action="admin.php?' . $_SERVER["QUERY_STRING"] . '" method="post">
	<h2>' . $translate['maininfo'] . '</h2>
	<fieldset>
		<strong>' . $translate['polls.question'] . '</strong>
		<input type="text" name="question" size="45" style="margin:3px;" value="'.setQ($info[0]).'" />
		<strong>' . $translate['settings'] . '</strong>
		<input type="checkbox" name="locked"'.(($info[3] == 1) ? ' checked="checked"' : '').' /> ' . $translate['polls.block'] . '<br />
		<input type="checkbox" name="clear_votes" /> ' . $translate['polls.clear'] . '
	</fieldset>
	
	<h2>' . $translate['polls.answers'] . '</h2>
	<table style="margin-left:20px;">'.n;
		$answers = explode ('#', $info[1]);
		$votes = explode ('#', $info[2]);
		foreach ($answers as $index => $value) $out .= '<tr><td><input type="text" value="'.setQ($value).'" name="poll-answers[]" /></td><td>&nbsp; &nbsp;<input type="text" name="poll-votes[]" style="width:40px;" value="'.$votes[$index].'" /></td></tr>';
		$out .= '</table>
	</fieldset>
	<input type="submit" name="ok" value="' . $translate['save'] . '" />
	<input type="reset" value="' . $translate['reset'] . '" />
</form>';
break;





/*--- Mazanie ankety ---*/

case 'delete':
	$out = HeadIfPost ($translate['polls.drop']);
	if (isset ($_REQUEST['ok'])) {
		if ($_REQUEST['ok'] != $translate['yes']) Header ('Location: ?what=addons&mod=polls');
		if (@mysql_query ("DELETE FROM {$prefix}_polls WHERE id = '".adjust($_REQUEST["id"],true)."'".FILTER." LIMIT 1"))
		Header ('Location: ?what=polls');
		else $out .= GetIcon ('error', $translate['failureact']);
	} else {
		$out .= '<form action="admin.php?' . $_SERVER['QUERY_STRING'] . '" method="post">
	<fieldset>
		<strong>' . $translate['sureact'] . '</strong>
		<input type="submit" name="ok" value="' . $translate['yes'] . '" />
		<input type="submit" name="ok" value="' . $translate['no'] . '" />
	</fieldset>
</form>';};
break;};