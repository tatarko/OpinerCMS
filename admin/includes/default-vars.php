<?php

/*--- Header ---*/

$type = defined ('xml') ? 'xml' : 'html';
Header ('Content-Type: text/' . $type . '; charset=UTF-8');



/*--- Definicie ---*/

define ('in', '0012012062601');
define ('SystemInfo', 'Opiner CMS~$~1.6 Angelina~$~Tomáš Tatarko~$~http://opiner.tatarko.sk/');
define ('n', "\n");
$labsprojects = array (
	'sotu'		=> 'statsontheuse',
	'ocaa'		=> 'ajaxadmin',
);

$dot = strrpos ($_SERVER['SCRIPT_NAME'], '/');
$filename = ($dot === false) ? $_SERVER['SCRIPT_NAME'] : substr ($_SERVER['SCRIPT_NAME'], $dot + 1);
$linker = str_replace ($filename, '', $_SERVER['PHP_SELF']);
$linker = 'http://' . $_SERVER['SERVER_NAME'] . $linker;
if (!defined ('_SiteLink')) define ('_SiteLink', str_replace ('www.', '', $linker));
if (!defined ('_SiteFileName')) define ('_SiteFileName', $filename);
if (!defined ('_SiteForm')) define ('_SiteForm', _SiteLink . _SiteFileName . (($_SERVER['QUERY_STRING'] != '')?'?':'') . $_SERVER['QUERY_STRING']);
$sn = strtoupper (md5(_SiteLink));
$sn = array (substr ($sn, 0, 4), substr ($sn, 4, 4), substr ($sn, 8, 4), substr ($sn, 12, 4));
if (!defined ('_SerialNumber')) define ('_SerialNumber', implode ('-', $sn));

function langrep ($name) {
	global $translate;
	$s = $translate[$name];
	foreach (func_get_args() as $i => $v) $s = str_replace ('$' . $i, $v, $s);
	return $s;
};




/*--- Premenne ---*/

$tohead = array ();



/*--- Funkcie ---*/

function chyba ($coho) { echo "<p><b><span style='color:red'>Chyba (Error)</span></b> - $coho</p>"; };
?>