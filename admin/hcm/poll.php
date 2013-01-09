<?php

if (!isset ($param1) or $param1 == '') {$out = ''; break;};
if (!isset ($param2) or array_search ($param2, array ('none', 'left', 'right')) === false) $param2 = 'none';
if (!isset ($param3) or $param3 == '') $param3 = 200;
if (false === ($info = mysql_fetch_row (mysql_query ("SELECT * FROM `{$prefix}_polls` WHERE `id` = " . adjust ($param1, true) . " LIMIT 1")))) {$out = ''; return;};
$tohead = array_merge ($tohead, array ('<link rel="stylesheet" href="media/styling.php?what=polls" type="text/css" />'));
$out = ($param2 == 'none') ? '<div class="opiner-poll" style="width:'.$param3.'px;">'.n : '<div class="opiner-poll" style="float:'.$param2.';width:'.$param3.'px;">'.n;
$out .= '<span class="opiner-poll-title" style="width:'.$param3.'px;">'.$info[2].'</span>'.n;
if (false === (mysql_fetch_row (mysql_query ("SELECT id FROM {$prefix}_iplog WHERE ip = '" . adjust ($_SERVER['REMOTE_ADDR']) . "' AND what = 'poll-$param1'"))) and $info[5] == 0) {
	$out .= '<form action="./'.rwl('stranka', 'vote').'" method="post" class="opiner-poll-form">'.n;
	$out .= '<input type="hidden" name="id" value="'.$param1.'" />'.n;
	$out .= '<input type="hidden" name="id" value="'.$param1.'" />'.n;
	$answers = explode ('#', $info[3]);
	$mc = substr (microtime (true), -3);
	foreach ($answers as $index => $value)
	$out .= '<input type="radio" name="vote" value="'.$index.'" id="p'.$mc.$index.'"'.(($index==0)?' checked="checked"':'').' /> <label for="p'.$mc.$index.'">'.$value.'</label><br />'.n;
	$out .= '<div style="text-align:center;"><input type="submit" name="ok" value="' . $translate['vote'] . '" /></div>'.n;
	$out .= '</form>'.n;
} else {
	$answers = explode ('#', $info[3]);
	$votes = explode ('#', $info[4]);
	$kolko = array_sum ($votes);
	$toped = $votes;
	rsort ($toped);
	$top = $toped[0];
	foreach ($answers as $index => $value) {
		$percent = ($top == 0) ? 0 : round ($votes[$index] / $top, 2);
		$per = ($kolko == 0) ? 0 : round ($votes[$index] / $kolko, 2) * 100;
		$sirka = round ($param3 * $percent, 0);
		$out .= $value.' <sub>['.oddel($votes[$index]).'/'.$per.'%]</sub><br />'.n.'<div class="opiner-poll-vote" style="width:'.$sirka.'px;"></div>'.n.'<div class="opiner-poll-hr" /></div>'.n;
	};
	$out .= langrep ('arts.voted', $kolko);
};
$out .= '</div>';

?>