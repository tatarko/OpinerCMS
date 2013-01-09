<?php

if (!isset ($param2)) $param2 = './';
if (!isset ($param1) or $param1 == '') $out = '';
else {
	$out = '<h2>' . $translate['comments.add.title'] . '</h2>' . n;
	$out .= (ONLINE) ? '<p>' . $translate['comments.add.note1'] . '</p>' .n:
	'<p>' . $translate['comments.add.note2'] . '</p>' . n;
	if (!ONLINE) $out .= '<script language="JavaScript" type="text/javascript">
	function chechingform(formular){
	if (formular.meno.value=="") {alert("' . $translate['comments.emptyname'] . '");formular.meno.focus();return false;
	} else if (formular.as.value=="" || formular.as.value=="0-9") {alert("' . $translate['comments.emptycaptcha'] . '");formular.as.focus();return false;
	} else if (formular.txt.value=="") {alert("' . $translate['comments.emptytext'] . '");return false;} else return true;
	}</script>' . n;
	$out .= '<form action="./" method="post" OnSubmit="return chechingform(this);">
	<input type="hidden" name="page" value="comment-add" />
	<input type="hidden" name="id" value="' . $param1 . '" />
	<input type="hidden" name="ref" value="' . $param2 . '" />'.n;
	if (!ONLINE) {
		if (isset ($_COOKIE['comments-default-values'])) {
			$default = explode ('~$~', $_COOKIE['comments-default-values']);
			$out .= '<strong>' . $translate['comments.add.name'] . '*</strong><br />
<input type="text" name="meno" value="' . $default[0] . '" /><br /><br />
<strong>' . $translate['comments.add.email'] . '</strong><br />
<input type="text" name="mail" value="' . $default[1] . '" /><br />
<em>' . langrep ('comments.emailnote', '<a href="http://www.gravatar.com/" rel="nofollow" target="_blank">Gravatar</a>') . '</em><br /><br />
<strong>' . $translate['comments.add.web'] . '</strong><br />
<input type="text" name="web" value="' . $default[2] . '" /><br /><br />
' . (($_CONFIG['antispam']==1)?'<table>
<tr><td><strong>' . $translate['comments.add.captcha'] . '*</strong></td><td rowspan="2"><img src="media/captcha.php" alt="" /></td></tr>
<tr><td><input type="text" name="as" value="0-9" size="5" /></td></tr>
</table><br />
' : '') . '<strong>' . $translate['comments.add.text'] . '*</strong><br />' . n;
		} else {
			$out .= '<strong>' . $translate['comments.add.name'] . '*</strong><br />
<input type="text" name="meno" /><br /><br />
<strong>' . $translate['comments.add.email'] . '</strong><br />
<input type="text" name="mail" /><br />
<em>' . langrep ('comments.emailnote', '<a href="http://www.gravatar.com/" rel="nofollow" target="_blank">Gravatar</a>') . '</em><br /><br />
<strong>' . $translate['comments.add.web'] . '</strong><br />
<input type="text" name="web" /><br /><br />
' . (($_CONFIG['antispam']==1)?'<table>
<tr><td><strong>' . $translate['comments.add.captcha'] . '*</strong></td><td rowspan="2"><img src="media/captcha.php" alt="" /></td></tr>
<tr><td><input type="text" name="as" value="0-9" size="5" /></td></tr>
</table><br />
' : '') . '<strong>' . $translate['comments.add.text'] . '*</strong><br />' . n;
		};
	};
	$out .= TexylaAdd ('txt').n.'<br />
<input type="submit" value="' . $translate['add'] . '" /></form>' . n;
};

?>