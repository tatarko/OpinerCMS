<?php
if (defined ("in"))
$themecolor = 'Chrome';
else {
	Header ("Content-type: text/css; charset=UTF-8");
?>
h2 { border-color: #bbb; }
h3 { color: #040d04; }
blockquote, code { border-color: #081a08; border-left-color: #174d17; background: #48ed48; }
a { color: #8FC0CE; }
a:hover { color: #A0D6E5; }
input[type="text"], input[type="password"], select, textarea { border-color: #D4D4D4; }
input[type="text"]:hover, input[type="password"]:hover, select:hover, textarea:hover { border-color: #E2E2E2; }
input[type="text"]:focus, input[type="password"]:focus, select:focus, textarea:focus { border-color: #9B9B9B; }
input[type="submit"], input[type="reset"], input[type="button"] { background-color: #6C6C6C; border-color: #373737; }
input[type="submit"]:hover, input[type="reset"]:hover, input[type="button"]:hover { background-color: #949494; border-color: #949494; }
#panel { background-color: #949494; }
#menu { background-color: #C9C9C9; }
#menu > ul > li > a { color: #1f661f; }
#menu > ul > li > ul { background-color: #C9C9C9; }
.infobox { background-color: #F8F8F8; border-color: #E7FFE7; }
#admintable th { background-color: #949494; border-color: #949494; }
#admintable tr td.imgprev img, img.prev { border-color: #1f661f; }
#pagelinks a { background-color: #c1ffc1; border-color: #8bff8b; color: #3ecc3e; }
#homepage td { background-color: #E9E9E9; }
#homepage td h3 { background-color: #969696; }
#homepage td h3 a { color: #FEFEFE; }
#plugins { background-color: #C9C9C9; border-color: #1f661f; }
div.album { background-color: #C9C9C9; }
#editGallery h3 { color: #333; text-shadow: 1px 1px 0 #fff; }
#fileman_lista { background: #e5e5e5 url('../img/bg.png') repeat-x left top; color: #bbb; }
#fileman_lista a { color: #666; text-shadow: 1px 1px 0 #fff; cursor: pointer; }
#fileman_lista a:hover { color: #888; }
#filemanager .actions { border-top-color: #ddd; }
#opiner-text { background-color: #f7f7f7; border-color: #ddd; }
#opiner-text-toolbar input { border-color: #ddd; }
#opiner-text-toolbar{ border-color: #ddd; }
<?php
};
?>