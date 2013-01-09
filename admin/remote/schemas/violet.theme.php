<?php
if (defined ("in"))
$themecolor = 'Fialky';
else {
	Header ("Content-type: text/css; charset=UTF-8");
?>
h3 { color: #0a010d; }
blockquote, code { border-color: #14031a; border-left-color: #3d084d; background: #be18ed; }
a { color: #8f12b3; }
a:hover { color: #b817e6; }
input[type="text"], input[type="password"], select, textarea { border-color: #b817e6; }
input[type="text"]:hover, input[type="password"]:hover, select:hover, textarea:hover { border-color: #a315cc; }
input[type="text"]:focus, input[type="password"]:focus, select:focus, textarea:focus { border-color: #ad16d9; }
input[type="submit"], input[type="reset"], input[type="button"] { background-color: #8f12b3; border-color: #660d80; }
input[type="submit"]:hover, input[type="reset"]:hover, input[type="button"]:hover { background-color: #8f12b3; border-color: #660d80; }
#panel { background-color: #8f12b3; }
#menu { background-color: #8f12b3; }
#menu > ul > li > a { color: #520a66; }
#menu > ul > li > ul { background-color: #ff27ff; }
.infobox { background-color: #ff53ff; border-color: #ff4eff; }
#admintable th { background-color: #b817e6; border-color: #8f12b3; }
#admintable tr td.imgprev img, img.prev { border-color: #520a66; }
#pagelinks a { background-color: #ff41ff; border-color: #ff2fff; color: #a315cc; }
#homepage td { background-color: #ff4eff; }
#homepage td h3 { background-color: #ff34ff; }
#homepage td h3 a { color: #3d084d; }
#plugins { background-color: #ff4eff; border-color: #520a66; }
<?php
};
?>