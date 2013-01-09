<?php
if (!defined ('in')) exit();
$pag = (isset ($_GET['kniha']) and $_GET['kniha'] != '') ? $_GET['kniha'] : 1;
$_META['keywords'] = $translate['gbook.keys'];
$_META['description'] = $translate['gbook.desc'];
$title .= $sep . $translate['links.gbook'];
$out = HcmParser('[hcm]com,kniha[/hcm]');
$out = str_replace ("<h1 align=\"center\">{$translate['comments']}</h1>", "<h1 align=\"center\">{$translate['links.gbook']}</h1>", $out);
if ($out == '') {
	header("HTTP/1.0 404 Not Found");
	$out .= '<h1 align="center">' . $translate['links.gbook']  .'</h1>
<p>' . $translate['er3'] . '</p>';
};