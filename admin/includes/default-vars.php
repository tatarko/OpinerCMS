<?php

/*--- Header ---*/

$type = defined ('xml') ? 'xml' : 'html';
Header ('Content-Type: text/' . $type . '; charset=UTF-8');



/*--- Definicie ---*/

define ('in', '0012015010101');
define ('SystemInfo', 'Opiner CMS~$~1.6.2 Angelina~$~Tomáš Tatarko~$~http://opiner.tatarko.sk/');
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


class OpinerAutoLoader {

	protected static $map = array(
		'TemplateClass' => 'admin/includes/TemplateClass.php',
		'texyla'		=> 'codes/texyla/texyla.php',
		'plugin'		=> 'admin/includes/pluginClass.php',
		'image'			=> 'admin/includes/image.class.inc.php',
		'wdriver'		=> 'admin/includes/wysiwyg-class.php',
		'OpinerText'	=> 'admin/opiner-text/OpinerText.php',
		'loadPlugin'	=> 'admin/includes/pluginClass.php',
	);

	public static function getRoot($file) {

		return dirname(__FILE__) . '/../../' . $file;
	}

	public static function addToMap($class, $file) {

		if(!file_exists(self::getRoot($file))) {

			trigger_error(sprintf('File %s for autoloading class %s does not exists!', $file, $class));
		}

		self::$map[$class] = $file;
	}

	public static function loadClass($class) {

		if(!class_exists($class, false) && isset(self::$map[$class])) {

			require_once self::getRoot(self::$map[$class]);
		}
	}

	public static function __callStatic($function, $args) {

		if(!function_exists($function) && isset(self::$map[$function])) {

			require_once self::getRoot(self::$map[$function]);
		}

		return call_user_func_array($function, $args);
	}
}

spl_autoload_register(array('OpinerAutoLoader', 'loadClass'));