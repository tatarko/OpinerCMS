<?php

$json = array (
	'protocol'      => '1.0',
	'generator'     => 'Pavio 1.0',
	'product'       => 'Pavio',
	'version'       => '1.0',
	'timestamp'     => time(),
	'datetime'      => date('r'),
	'message'       => 'Wrong request',
	'code'          => '201',
);

include('../../media/get-config.php');
include('../../admin/includes/default-functions.php');

if (isset ($_REQUEST['id'], $_REQUEST['comment'])
and !empty ($_REQUEST['id'])
and !empty ($_REQUEST['comment']))
{
	if (mysql_query ("INSERT INTO `{$prefix}_comments` VALUES (0, 'image_" . intval ($_REQUEST['id']) . "', 'undefined', NOW(), '" . adjust (strip_tags($_REQUEST['comment'])) . "', '" . $_SERVER['REMOTE_ADDR'] . "', NULL, NULL);"))
	{
		$json['message'] = 'Komentár úspešne pridaný';
		$json['code'] = '100';
		$json['comment'] = array (
			'id'    	=> mysql_insert_id(),
			'timestamp'	=> time(),
			'datetime'      => date('r'),
			'dateformat'    => date('d.m.Y H:i'),
			'entry'         => htmlspecialchars(strip_tags($_REQUEST['comment'])),
		);
	}
	else
	{
		$json['message'] = 'Nepodarilo sa pridať komentár';
		$json['code'] = 202;
	}
}
else if (isset ($_REQUEST['comments']))
{
	if (false === ($sql = mysql_query ("SELECT `id`, UNIX_TIMESTAMP(`added`) as `time`, `text` FROM `{$prefix}_comments` WHERE `kde` = 'image_" . intval ($_REQUEST['comments']) . "' ORDER BY `added` DESC LIMIT 10")))
	{
		$json['message'] = 'Nedá sa spracovať požiadavka';
		$json['code'] = 203;
		return;
	}
	$json['message'] = 'Výpis komentárov';
	$json['code']   = 100;
	$json['count'] = mysql_num_rows($sql);
	while ($data = mysql_fetch_assoc ($sql))
	@$json['comment'][++$i] = array (
		'id'    	=> $data['id'],
		'timestamp'	=> $data['time'],
		'datetime'      => date('r', $data['time']),
		'dateformat'    => date('d.m.Y H:i', $data['time']),
		'entry'         => htmlspecialchars($data['text']),
	);
}

echo json_encode($json);

?>