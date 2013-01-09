<?php
TexyConfigurator::safeMode($texy);
$texy->setOutputMode(Texy::XHTML1_TRANSITIONAL);
$texy->htmlOutputModule->removeOptional = false;
$texy->allowed['link/definition'] = false;
$texy->allowed['image/definition'] = false;
$texy->allowed['heading/surrounded'] = false;
$texy->allowed['heading/underlined'] = false;
$texy->mergeLines = false;
$texy->allowed['phrase/ins'] = false;		// ++inserted++
$texy->allowed['phrase/del'] = false;		// --deleted--
$texy->allowed['phrase/sup'] = true;		// ^^superscript^^
$texy->allowed['phrase/sub'] = true;		// __subscript__
$texy->allowed['phrase/cite'] = false;		// ~~cite~~
$texy->allowed['deprecated/codeswitch'] = true;	// `=code
$texy->allowed['tags'] = false;
$texy->linkModule->forceNoFollow = true;
$texy->allowed['link/link'] = true;
$texy->allowed['link/url'] = true;
$texy->allowed['link/email'] = true;
$useFSHL = false;
$texy->allowed['emoticon'] = true;
include dirname(__FILE__) . '/../emoticons/silk/cfg.php';
$addTargetBlank = true;
?>