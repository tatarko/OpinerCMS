<?php
if (defined ('in'))
$themecolor = 'Pinky';
else {
	Header ('content-type: text/css; charset=UTF-8');
?>
h3 { color: #B742A0; }
blockquote, code { border-color: #D0B8D6; border-left-color: #9661A3; background: #F0E8F2; }
a { color: #AF3BA4; }
a:hover { color: #FF4EA3; }
input[type="text"], input[type="password"], select, textarea { border-color: #E29ED8; }
input[type="text"]:hover, input[type="password"]:hover, select:hover, textarea:hover { border-color: #D87BBB; }
input[type="text"]:focus, input[type="password"]:focus, select:focus, textarea:focus { border-color: #E29ECC; }
input[type="submit"], input[type="reset"], input[type="button"] { background-color: #C172A7; border-color: #BA398F; }
input[type="submit"]:hover, input[type="reset"]:hover, input[type="button"]:hover { background-color: #C681A4; border-color: #C4508A; }
#panel { background-color: #AF467B; }
#menu { background-color: #F2DCE7; }
#menu > ul > li > a { color: #B75084; }
#menu > ul > li > ul { background-color: #F2DCE7; }
.infobox { background-color: #F9F4F7; border-color: #E2C5D4; }
#admintable th { background-color: #C1729A; border-color: #BA3979; }
#admintable tr td.imgprev img, img.prev { border-color: #F759F4; }
#pagelinks a { background-color: #FFD6FF; border-color: #FFA8F7; color: #DD58D2; }
#homepage td { background-color: #EFDAED; }
#homepage td h3 { background-color: #BC5E95; }
#homepage td h3 a { color: #EFDAEF; }
#plugins { background-color: #F7F2F7; border-color: #E2CCE2; }
<?php
};
?>