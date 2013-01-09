<?php
if (defined ("in"))
$themecolor = 'Žlté slniečko';
else {
	Header ("Content-type: text/css; charset=UTF-8");
?>
h3 { color: #0c0c03; }
blockquote, code { border-color: #171705; border-left-color: #45450f; background: #d6d62f; }
a { color: #a1a124; }
a:hover { color: #cfcf2e; }
input[type="text"], input[type="password"], select, textarea { border-color: #cfcf2e; }
input[type="text"]:hover, input[type="password"]:hover, select:hover, textarea:hover { border-color: #b8b829; }
input[type="text"]:focus, input[type="password"]:focus, select:focus, textarea:focus { border-color: #c4c42b; }
input[type="submit"], input[type="reset"], input[type="button"] { background-color: #a1a124; border-color: #73731a; }
input[type="submit"]:hover, input[type="reset"]:hover, input[type="button"]:hover { background-color: #a1a124; border-color: #73731a; }
#panel { background-color: #a1a124; }
#menu { background-color: #a1a124; }
#menu > ul > li > a { color: #5c5c14; }
#menu > ul > li > ul { background-color: #ffff4d; }
.infobox { background-color: #ffffa3; border-color: #ffff99; }
#admintable th { background-color: #cfcf2e; border-color: #a1a124; }
#admintable tr td.imgprev img, img.prev { border-color: #5c5c14; }
#pagelinks a { background-color: #73731a; border-color: #171705; color: #45450f; }
#homepage td { background-color: #ffff99; }
#homepage td h3 { background-color: #ffff66; }
#homepage td h3 a { color: #45450f; }
#plugins { background-color: #ffff99; border-color: #5c5c14; }
<?php
};
?>