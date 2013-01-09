<?php
if (!isset ($_GET['what']) or $_GET['what'] == '') exit ();
include ('get-config.php');
include ('../templates/' . $_CONFIG['template'] . '/config.php');
Header ('Content-type: text/css');
$param = explode ('---', $_GET['what']);
switch ($param[0]) {



/*--- Styles for polls ---*/

case 'polls':
echo "/* Polls System Style */

.opiner-poll{background-color:" . $TempConfig['theme-colors']['ap1'] . ";margin:0 10px 5px;padding:5px 8px;border:1px solid #808080;-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;}
.opiner-poll-title{color:#808080;font-size:14px;border-bottom:1px dashed #808080;width:100%;display:block;}
.opiner-poll-form{margin:0;padding:0;}
.opiner-poll-vote{background:" . $TempConfig['theme-colors']['ap4'] . ";border-left:1px solid skyblue;height:12px;width:100%;display:block;-moz-border-radius:2px;-webkit-border-radius:2px;border-radius:2px;}
.opiner-poll-hr{border-bottom:1px dashed #808080;margin:5px 0 3px;}";
break;



/*--- Styles for Gallery ---*/

case 'gallery':
echo "/* Gallery System Style */

.OpinerGalleryHead{margin:10px 0 5px;padding:2px;border-bottom:1px dashed ".$TempConfig['theme-colors']['ap3'].";}
.OpinerGalleryHead a{text-decoration:none;}
.OpinerGalleryBox{margin-bottom:35px;background:".$TempConfig['theme-colors']['ap2'].";border:1px solid ".$TempConfig['theme-colors']['ap3'].";width:100%;-moz-border-radius:5px;}
.OpinerGalleryRow1{padding:5px 10px;}
.OpinerGalleryRow1 img{padding:7px;background:".$TempConfig['theme-colors']['ap1'].";border:1px solid ".$TempConfig['theme-colors']['ap3'].";-moz-border-radius:5px;margin:5px;}
.OpinerGalleryRow2{padding:5px 10px 5px 0;width:100%;vertical-align:top;}";
break;



/*--- Styles for FastText ---*/

case 'fasttext':
echo "/* FastText Style */

body{margin:0;padding:0;min-height:248px;}
.link{text-align:center;margin:0;padding:0;border:1px solid " . $TempConfig['theme-colors']['ap3'] . ";background:" . $TempConfig['theme-colors']['ap2'] . ";border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px;}
a{text-decoration:none;color:#808080;}
.text1, .text2{border:1px solid " . $TempConfig['theme-colors']['ap3'] . ";padding:3px;font:normal 9px 'Verdana',serif;color:#a0a0a0;margin:5px;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px;}
.text1 b, .text2 b, form{font-size:10px;color:#808080;}
.text1{background:" . $TempConfig['theme-colors']['ap2'] . ";}
.text2{background:" . $TempConfig['theme-colors']['ap1'] . ";}
input{font-size:10px;width:120px;margin:1px;}";
break;
default:break;};  
?>