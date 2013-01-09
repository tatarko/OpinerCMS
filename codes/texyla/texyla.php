<?php
if(!defined('TEXYLA_CLASS_NOT_TEXY_FOUND')){define('TEXYLA_FILE_NOT_FOUND', 1);}
if(!defined('TEXYLA_OLD_PHP')){define('TEXYLA_OLD_PHP',2);}
if(!defined('TEXYLA_ACCESS_DENIED')){define('TEXYLA_ACCESS_DENIED',3);}
if(!defined('TEXYLA_ICONV_MISSING')){define('TEXYLA_ICONV_MISSING', 5);}
if(!defined('TEXYLA_TEXY_NOT_FOUND')){define('TEXYLA_TEXY_NOT_FOUND', 6);}
if(!defined('TEXYLA_FSHL_NOT_FOUND')){define('TEXYLA_FSHL_NOT_FOUND', 7);}
if(!defined('TEXYLA_FILE_CFG_NOT_FOUND')){define('TEXYLA_FILE_CFG_NOT_FOUND', 8);}
$pathTexy = dirname(__FILE__) . '/texy/texy.compact.php';
$pathFSHL = dirname(__FILE__) . '/fshl/fshl.php';
if(!file_exists($pathTexy)){trigger_error(texylaErrorMsg(TEXYLA_TEXY_NOT_FOUND), E_USER_ERROR);}
else{require_once($pathTexy);}
if(!file_exists($pathFSHL)){}
else{include_once(dirname(__FILE__) . '/fshl/fshl.php');}
require_once(dirname(__FILE__) . '/texy/texy.compact.php');
require_once(dirname(__FILE__) . '/fshl/fshl.php');
if(!empty($_POST['texylaAjax'])){
	removeMagicQuotesGpc();
	$charset = 'utf-8';
	if(!empty($_GET['texylaCharset'])) {
		$charset = $_GET['texylaCharset'];}
	header('Content-type: text/html; charset=' . $charset);
	die(texyla(@$_POST['texylaContent'], @$_POST['texylaTexyCfg'], $charset));}

function texyla($content, $texyCfg, $charset = 'utf-8', $oneLine = false){
	if(empty($content)){return $content;}
	if((!function_exists('iconv')) && ($charset != 'utf-8')){trigger_error(texylaErrorMsg(TEXYLA_ICONV_MISSING), E_USER_ERROR);}
	$texyCfg = texylaTexyCfg($texyCfg);
	$texyCfgFile = dirname(__FILE__) . '/cfg/' . $texyCfg . '.php';
	if(!is_bool($oneLine)) {$oneLine = false;}
	if(!class_exists('Texy')) {trigger_error(texylaErrorMsg(TEXYLA_CLASS_NOT_TEXY_FOUND, $texyFile), E_USER_ERROR);}
	$texy = new Texy();
	$texy->encoding = $charset;
	Texy::$advertisingNotice = false;
	if($texyCfg == 'webalize') {$content = (strtolower($charset) == 'utf-8' ? $content : iconv($charset, 'utf-8', $content));$addChar = '';return Texy::webalize($content, $addChar);}
	if(!file_exists($texyCfgFile)) {return texylaErrorMsg(TEXYLA_FILE_CFG_NOT_FOUND, $texyCfgFile);}
	if(!include($texyCfgFile)){return texylaErrorMsg(TEXYLA_ACCESS_DENIED, $texyFile);}
	if((function_exists('TexylaFSHLBlockHandler')) && (class_exists('fshlParser')) && (!empty($useFSHL)) && ($useFSHL === true)) {$texy->addHandler('block', 'TexylaFSHLBlockHandler');}
	if(!empty($addTargetBlank) && $addTargetBlank === true) {return preg_replace_callback("~<a href~iU", "texylaAddTargetBlank", $texy->process($content, $oneLine));}
	return $texy->process($content, $oneLine);
}

function texylaAddTargetBlank($text) {return '<a target="_blank" href';}
function texylaTexyCfg($cfg){switch($cfg){case 'admin': return 'admin'; break;case 'oneline': return 'oneline'; break;case 'webalize': return 'webalize'; break;default: return 'forum';}}
function texylaErrorMsg($numMsg, $cesta = ''){$msg = '';switch ($numMsg){
case 1: $msg .= '<p>Neexistuje třída Texy()!</p>'; break;case 2: $msg .= '<p>Nepodporovaná verze php.</p>'; break;case 3: $msg .= '<p>Nejsou správně nastavena přístupová práva k souboru ' . $cesta . '</p>'; break;
case 5: $msg .= '<p>Chybí funkce iconv, která slouží k změně znakové sady zpracovávaného textu. Nelze pokračovat.</p>'; break;case 6:$msg .= '<p>Adresář <b>./texyla/texy/</b> neobsahuje soubor <b>texy.compat.php</b>, který je důležitý pro zpracování textu (obsahuje třídu Texy). </p>';
$msg .= '<p>Pokud nemáte Texy, stáhněte si ji ze stránek <a href="http://texy.info/download">http://texy.info/download</a>.</p>';$msg .= '<p>Věnujte prosím pozornost licenci, pokud chcete Texy používat v closed source systémech, kontaktujte autora Texy, kterým je David Grudl.</p>';$msg .= '<p>Po rozbalení staženého balíku najděte adresář <b>texy.compact</b>, a v něm obsažený soubor <b>texy.compact.php</b> nakopírujte do adresáře Texyly <b>./texyla/texy/</b>';break;
case 7:$msg .= '<p>Adresář <b>./texyla/fshl/</b> neobsahuje soubor <b>fshl.php</b>, který obsahuje třídu fshlParser.</p><p>Tato třída se stará o obarvování publikovaných zdrojových kódů</p>';$msg .= '<p>Pokud nemáte Fshl a chcete ho použít, stáhněte si ho ze stránek <a href="http://www.hvge.sk/scripts/fshl/">http://www.hvge.sk/scripts/fshl/</a>.</p>';$msg .= '<p>Věnujte prosím pozornost licenci, pokud chcete Fshl používat v closed source systémech, kontaktujte autora Fshl, kterým je Juraj `hvge` Ďurech.</p>';
$msg .= '<p>Po rozbalení staženého balíku nakopírujte jeho obsah do připraveného adresáře <b>./texyla/fshl/</b>';$msg .= '<p>Nechcete-li Fshl používat, zakomentujte řádek č.: %s v souboru texyla.php</p>';break;
case 8 :$msg .= '<p>Nepodařilo se načíst soubor s konfigurací pro Texy, nelze pokračovat.';break;default: $msg .= '<p>Pokoušíte se volat neznámé chybové hlášení. Hodnota je: ' . $numMsg .'</p>';}die($msg);}
function TexylaFSHLBlockHandler($invocation, $blocktype, $content, $lang, $modifier){if ($blocktype !== 'block/code'){return $invocation->proceed();}
$lang = strtoupper($lang);if ($lang == 'JAVASCRIPT'){$lang = 'JS';}$parser = new fshlParser('HTML_UTF8', P_TAB_INDENT);if (!$parser->isLanguage($lang)){return $invocation->proceed();}
$texy = $invocation->getTexy();$content = Texy::outdent($content);$content = $parser->highlightString($lang, $content);$content = $texy->protect($content, Texy::CONTENT_BLOCK);$elPre = TexyHtml::el('pre');
if ($modifier){$modifier->decorate($texy, $elPre);}$elPre->attrs['class'] = strtolower($lang);$elCode = $elPre->create('code', $content);return $elPre;}
function removeMagicQuotesGpc(){if(get_magic_quotes_gpc()){$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST, &$_FILES);while (list($key, $val) = each($process)){
foreach ($val as $k => $v){unset($process[$key][$k]);if (is_array($v)){$process[$key][($key < 5 ? $k : stripslashes($k))] = $v;$process[] =& $process[$key][($key < 5 ? $k : stripslashes($k))];}
else{$process[$key][stripslashes($k)] = stripslashes($v);}}}}}
?>