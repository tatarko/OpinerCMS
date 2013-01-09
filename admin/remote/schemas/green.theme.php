<?php
if (defined ("in"))
$themecolor = 'Zelená je tráva';
else {
	Header ("Content-type: text/css; charset=UTF-8");
?>
h3 { color: #040d04; }
blockquote, code { border-color: #081a08; border-left-color: #174d17; background: #48ed48; }
a { color: #36b336; }
a:hover { color: #45e645; }
input[type="text"], input[type="password"], select, textarea { border-color: #45e645; }
input[type="text"]:hover, input[type="password"]:hover, select:hover, textarea:hover { border-color: #3ecc3e; }
input[type="text"]:focus, input[type="password"]:focus, select:focus, textarea:focus { border-color: #41d941; }
input[type="submit"], input[type="reset"], input[type="button"] { background-color: #36b336; border-color: #278027; }
input[type="submit"]:hover, input[type="reset"]:hover, input[type="button"]:hover { background-color: #36b336; border-color: #278027; }
#panel { background-color: #36b336; }
#menu { background-color: #36b336; }
#menu > ul > li > a { color: #1f661f; }
#menu > ul > li > ul { background-color: #74ff74; }
.infobox { background-color: #f6fff6; border-color: #e7ffe7; }
#admintable th { background-color: #45e645; border-color: #36b336; }
#admintable tr td.imgprev img, img.prev { border-color: #1f661f; }
#pagelinks a { background-color: #c1ffc1; border-color: #8bff8b; color: #3ecc3e; }
#homepage td { background-color: #e7ffe7; }
#homepage td h3 { background-color: #9aff9a; }
#homepage td h3 a { color: #174d17; }
#plugins { background-color: #e7ffe7; border-color: #1f661f; }
<?php
};
?>