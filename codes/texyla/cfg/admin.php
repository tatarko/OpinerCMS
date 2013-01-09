<?php
$texy->setOutputMode(Texy::XHTML1_TRANSITIONAL);
$texy->allowed['phrase/ins'] = true;		// ++inserted++
$texy->allowed['phrase/del'] = true;		// --deleted--
$texy->allowed['phrase/sup'] = true;		// ^^superscript^^
$texy->allowed['phrase/sub'] = true;		// __subscript__
$texy->allowed['phrase/cite'] = true;		// ~~cite~~
$texy->allowed['deprecated/codeswitch'] = true;	// `=code
# nadpisy
$texy->headingModule->balancing = TEXY_HEADING_FIXED;
$texy->mergeLines = true;
$texy->imageModule->fileRoot = dirname(__FILE__) . '/../../../';
$useFSHL = true;
$texy->allowed['emoticon'] = true;
include dirname(__FILE__) . '/../emoticons/silk/cfg.php';
$addTargetBlank = false; 
?>