<?php
$texy->setOutputMode(Texy::XHTML1_TRANSITIONAL);
$texy->htmlOutputModule->removeOptional = false;
$texy->allowed['link/definition'] = false;
$texy->allowed['image/definition'] = false;
$texy->allowed['heading/surrounded'] = false;
$texy->allowed['heading/underlined'] = false;
$texy->mergeLines = false;
$texy->allowed['phrase/strong+em'] = FALSE;  // ***strong+emphasis***
$texy->allowed['phrase/strong'] = FALSE;     // **strong**
$texy->allowed['phrase/em'] = FALSE;         // //emphasis//
$texy->allowed['phrase/em-alt'] = FALSE;     // *emphasis*
$texy->allowed['phrase/span'] = FALSE;       // "span"
$texy->allowed['phrase/span-alt'] = FALSE;   // ~span~
$texy->allowed['phrase/acronym'] = FALSE;    // "acro nym"((...))
$texy->allowed['phrase/acronym-alt'] = FALSE;// acronym((...))
$texy->allowed['phrase/code'] = FALSE;       // `code`
$texy->allowed['phrase/notexy'] = FALSE;     // ''....''
$texy->allowed['phrase/quote'] = FALSE;      // >>quote<<:...
$texy->allowed['phrase/quicklink'] = FALSE;  // ....:LINK
$texy->allowed['phrase/sup-alt'] = FALSE;    // superscript^2
$texy->allowed['phrase/sub-alt'] = FALSE;    // subscript_3
$texy->allowed['phrase/ins'] = FALSE;       // ++inserted++
$texy->allowed['phrase/del'] = FALSE;		// --deleted--
$texy->allowed['phrase/sup'] = FALSE;		//^^superscript^^
$texy->allowed['phrase/sub'] = FALSE;       // __subscript__
$texy->allowed['phrase/cite'] = FALSE;      // ~~cite~~
$texy->allowed['deprecated/codeswitch'] = FALSE;// `=...
$texy->allowedTags = FALSE;
$texy->linkModule->forceNoFollow = true;
$texy->allowed['link/link'] = true;
$texy->allowed['link/url'] = true;
$texy->allowed['link/email'] = true;
$useFSHL = false;
$texy->allowed['emoticon'] = true;
include dirname(__FILE__) . '/../emoticons/silk/cfg.php';
$addTargetBlank = true; 
?>