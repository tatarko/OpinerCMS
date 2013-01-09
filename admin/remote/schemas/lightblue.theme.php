<?php
if (defined ("in"))
$themecolor = 'Cyklaménová';
else {
	Header ("Content-type: text/css; charset=UTF-8");
?>
h3 { color: #040d0d; }
blockquote, code { border-color: #081a1a; border-left-color: #174d4d; background: #48eded; }
a { color: #36b3b3; }
a:hover { color: #45e6e6; }
input[type="text"], input[type="password"], select, textarea { border-color: #45e6e6; }
input[type="text"]:hover, input[type="password"]:hover, select:hover, textarea:hover { border-color: #3ecccc; }
input[type="text"]:focus, input[type="password"]:focus, select:focus, textarea:focus { border-color: #41d9d9; }
input[type="submit"], input[type="reset"], input[type="button"] { background-color: #36b3b3; border-color: #278080; }
input[type="submit"]:hover, input[type="reset"]:hover, input[type="button"]:hover { background-color: #36b3b3; border-color: #278080; }
#panel { background-color: #36b3b3; }
#menu { background-color: #36b3b3; }
#menu > ul > li > a { color: #1f6666; }
#menu > ul > li > ul { background-color: #74ffff; }
.infobox { background-color: #f6ffff; border-color: #e7ffff; }
#admintable th { background-color: #45e6e6; border-color: #36b3b3; }
#admintable tr td.imgprev img, img.prev { border-color: #1f6666; }
#pagelinks a { background-color: #278080; border-color: #081a1a; color: #174d4d; }
#homepage td { background-color: #e7ffff; }
#homepage td h3 { background-color: #9affff; }
#homepage td h3 a { color: #174d4d; }
#plugins { background-color: #e7ffff; border-color: #1f6666; }
<?php
};
?>