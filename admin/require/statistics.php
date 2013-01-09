<?php
if (!defined ('in')) exit ();
$out = HeadIfPost ($translate['stats']);
$out .= '<p class="right">
 <a href="?what=file-manager">' . TempIcon ('cat') . ' ' . $translate['fman'] . '</a>
 <a href="?what=file-manager&mod=download-manager">' . TempIcon ('cat') . ' ' . $translate['fman.down'] . '</a>
 <a href="?what=statistics">' . TempIcon ('cat') . ' ' . $translate['stats'] . '</a>
 <a href="?what=backup">' . TempIcon ('cat') . ' ' . $translate['backup'] . '</a>
 <a href="?what=labs">' . TempIcon ('cat') . ' ' . $translate['labsprojects'] . '</a>
</p>'.n;
$mod = (isset($_GET['mod'])) ? $_GET['mod'] : 'monthly';
$day = (isset($_GET['day'])) ? $_GET['day'] : date ('d');
$year = (isset($_GET['year'])) ? $_GET['year'] : date ('Y');
$month = (isset($_GET['month'])) ? $_GET['month'] : date ('m');
$limit = $_CONFIG['list_stats'];
function to2 ($num) { return (strlen ($num) == 1) ? "0$num" : $num; }





/*--- Stránkovanie ---*/

$out .= '<form action="admin.php?what=statistics" method="post">
<strong>' . $translate['stats.goto'] . '</strong>
<select name="mod" style="width:150px;">
	<option value="daily"'.(($mod=='daily')?' selected':'').'>' . $translate['stats.daily'] . '</option>
	<option value="weekly"'.(($mod=='weekly')?' selected':'').'>' . $translate['stats.weekly'] . '</option>
	<option value="monthly"'.(($mod=='monthly')?' selected':'').'>' . $translate['stats.monthly'] . '</option>
	<option value="yearly"'.(($mod=='yearly')?' selected':'').'>' . $translate['stats.yearly'] . '</option>
</select>
<select name="day" style="width:150px;">'.n;
for ($i = 1; $i <= 31; ++$i)
$out .= ($i == $day) ? '	<option value="'.to2($i).'" selected>'.to2($i).'</option>' : '	<option value="'.to2($i).'">'.to2($i).'</option>';
$out .= '</select>
<select name="month" style="width:150px;">'.n;
for ($i = 1; $i <= 12; ++$i)
$out .= ($i == $month) ? '	<option value="'.to2($i).'" selected>' . $translate['stats.' . to2($i)] . '</option>' : '	<option value="'.to2($i).'">'.$translate['stats.' . to2($i)].'</option>';
$out .= '</select>
<select name="year" style="width:150px;">'.n;
$query = @mysql_query ("SELECT DISTINCT DATE_FORMAT(kedy,'%Y') FROM {$prefix}_hits ORDER BY id ASC");
while ($i = @mysql_fetch_row ($query))
$out .= ($i[0] == $year) ? "\t<option value=\"$i[0]\" selected>$i[0]</option>\n" : "\t<option value=\"$i[0]\">$i[0]</option>\n";
$out .= '</select>
<input type="submit" value="OK" />
</form>'.n;





/*--- MOD ALL ---*/

if($mod == 'daily'){
class browser{
var $Name = "Unknown";
var $Version = "Unknown";
var $Platform = "Unknown";
var $UserAgent = "Not reported";
var $AOL = false;
function browser ($agent = '') {
$bd['platform'] = "Unknown";$bd['browser'] = "Unknown";$bd['version'] = "Unknown";$this->UserAgent = $agent;
if (eregi("win", $agent))$bd['platform'] = "Windows";elseif (eregi("mac", $agent))$bd['platform'] = "Mac OS X";elseif (eregi("linux", $agent))$bd['platform'] = "Linux";elseif (eregi("OS/2", $agent))$bd['platform'] = "OS/2";elseif (eregi("BeOS", $agent))$bd['platform'] = "BeOS";
if (eregi("opera",$agent)){$val = stristr($agent, "opera");if (eregi("/", $val)){$val = explode("/",$val);$bd['browser'] = $val[0];$val = explode(" ",$val[1]);$bd['version'] = $val[0];}else{$val = explode(" ",stristr($val,"opera"));$bd['browser'] = $val[0];$bd['version'] = $val[1];}
}elseif(eregi("webtv",$agent)){$val = explode("/",stristr($agent,"webtv"));$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("microsoft internet explorer", $agent)){$bd['browser'] = "MSIE";$bd['version'] = "1.0";$var = stristr($agent, "/");if (ereg("308|425|426|474|0b1", $var)){$bd['version'] = "1.5";}
}elseif(eregi("NetPositive", $agent)){$val = explode("/",stristr($agent,"NetPositive"));$bd['platform'] = "BeOS";$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("msie",$agent) && !eregi("opera",$agent)){$val = explode(" ",stristr($agent,"msie"));$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("mspie",$agent) || eregi('pocket', $agent)){$val = explode(" ",stristr($agent,"mspie"));$bd['browser'] = "MSPIE";$bd['platform'] = "WindowsCE";if (eregi("mspie", $agent))$bd['version'] = $val[1];else {$val = explode("/",$agent);$bd['version'] = $val[1];}
}elseif(eregi("chrome",$agent)){$val = explode(" ",stristr($agent,"chrome"));$val = explode("/",$val[0]);$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("safari",$agent)){$val = explode(" ",stristr($agent,"safari"));$val = explode("/",$val[0]);$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("flock",$agent)){$val = explode(" ",stristr($agent,"flock"));$val = explode("/",$val[0]);$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("galeon",$agent)){$val = explode(" ",stristr($agent,"galeon"));$val = explode("/",$val[0]);$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("Konqueror",$agent)){$val = explode(" ",stristr($agent,"Konqueror"));$val = explode("/",$val[0]);$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("icab",$agent)){$val = explode(" ",stristr($agent,"icab"));$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("omniweb",$agent)){$val = explode("/",stristr($agent,"omniweb"));$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("Phoenix", $agent)){$bd['browser'] = "Phoenix";$val = explode("/", stristr($agent,"Phoenix/"));$bd['version'] = $val[1];
}elseif(eregi("firebird", $agent)){$bd['browser']="Firebird";$val = stristr($agent, "Firebird");$val = explode("/",$val);$bd['version'] = $val[1];
}elseif(eregi("Firefox", $agent)){$bd['browser']="Firefox";$val = stristr($agent, "Firefox");$val = explode("/",$val);$bd['version'] = $val[1];
}elseif(eregi("mozilla",$agent) && eregi("rv:[0-9].[0-9][a-b]",$agent) && !eregi("netscape",$agent)){$bd['browser'] = "Mozilla";$val = explode(" ",stristr($agent,"rv:"));eregi("rv:[0-9].[0-9][a-b]",$agent,$val);$bd['version'] = str_replace("rv:","",$val[0]);
}elseif(eregi("mozilla",$agent) && eregi("rv:[0-9]\.[0-9]",$agent) && !eregi("netscape",$agent)){$bd['browser'] = "Mozilla";$val = explode(" ",stristr($agent,"rv:"));eregi("rv:[0-9]\.[0-9]\.[0-9]",$agent,$val);$bd['version'] = str_replace("rv:","",$val[0]);
}elseif(eregi("libwww", $agent)){if (eregi("amaya", $agent)){$val = explode("/",stristr($agent,"amaya"));$bd['browser'] = "Amaya";$val = explode(" ", $val[1]);$bd['version'] = $val[0];} else {$val = explode("/",$agent);$bd['browser'] = "Lynx";$bd['version'] = $val[1];}
}elseif(eregi("safari", $agent)){$bd['browser'] = "Safari";$bd['version'] = "";
}elseif(eregi("netscape",$agent)){$val = explode(" ",stristr($agent,"netscape"));$val = explode("/",$val[0]);$bd['browser'] = $val[0];$bd['version'] = $val[1];
}elseif(eregi("mozilla",$agent) && !eregi("rv:[0-9]\.[0-9]\.[0-9]",$agent)){$val = explode(" ",stristr($agent,"mozilla"));$val = explode("/",$val[0]);$bd['browser'] = "Netscape";$bd['version'] = $val[1];}
$bd['browser'] = ereg_replace("[^a-z,A-Z]", "", $bd['browser']);
$bd['version'] = ereg_replace("[^0-9,.,a-z,A-Z]", "", $bd['version']);
if (eregi("AOL", $agent)){$var = stristr($agent, "AOL");$var = explode(" ", $var);$bd['aol'] = ereg_replace("[^0-9,.,a-z,A-Z]", "", $var[1]);}else $bd['aol']='';
$this->Name = $bd['browser'];
$this->Version = $bd['version'];
$this->Platform = $bd['platform'];
$this->AOL = $bd['aol'];}
function params(){return array($this->Name, $this->Version, $this->Platform);}};
$browser = new browser;
if (isset ($_REQUEST['filter'])) {
	$key = str_replace ('"', '', $_REQUEST['filter']);
	$key = str_replace ("'", '', $key);
	$_SESSION['filter'] = $key;
	$where = " AND kde LIKE '%$key%'";
	$searchinput = $key;
} else {
	if (isset ($_SESSION['filter'])) {
		$key = str_replace ('"', '', $_SESSION['filter']);
		$key = str_replace ("'", '', $key);
		$where = " AND kde LIKE '%$key%'";
		$searchinput = $key;
	} else {
		$searchinput = '';
		$where = '';
	};
};
$out.='<form action="admin.php?'.$_SERVER['QUERY_STRING'].'" method="post">
' . $translate['stats.filter'] . ' <input type="text" name="filter" value="' . $searchinput . '" /> <input type="submit" value="' . $translate['stats.filter'] . '" />
</form>
<table id="admintable" cellspacing="3px">
<tr><th>' . $translate['stats.when'] . '</th><th>' . $translate['stats.where'] . '</th><th>' . $translate['stats.ip'] . '</th><th>' . $translate['stats.os'] . '</th><th>' . $translate['stats.browser'] . '</th></tr>'.n;$i=0;
if(!isset($_GET["pag"]))$pag=1;else $pag=$_GET["pag"];$limit2=($pag-1)*$limit;
$sql=mysql_query("SELECT DATE_FORMAT(kedy,'%H:%i:%s'),kde,ip,browser FROM {$prefix}_hits WHERE DATE_FORMAT(kedy,'%Y-%m-%d') = '$year-$month-$day'$where ORDER BY id DESC LIMIT $limit2,$limit");
while($tab=mysql_fetch_row($sql)){
$browser->browser ($tab[3]);
$data = $browser->params();
$out .= "<tr><td>$tab[0]</td><td><a href='./$tab[1]' target='_blank'>$tab[1]</a></td><td>$tab[2]</td><td>$data[2]</td><td>$data[0] ($data[1])</td></tr>\n";
if (++$i % 20 == 0) $out .= '<tr><th>' . $translate['stats.when'] . '</th><th>' . $translate['stats.where'] . '</th><th>' . $translate['stats.ip'] . '</th><th>' . $translate['stats.os'] . '</th><th>' . $translate['stats.browser'] . '</th></tr>'.n;};
$out.='</table>';
$kolko=@mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM {$prefix}_hits WHERE DATE_FORMAT(kedy,'%Y-%m-%d')='$year-$month-$day'$where"));
if($kolko[0]>$limit){$out.="<p align='center'><strong>{$translate['stats.acpage']}: $pag</strong><br />\n";$endp=0;};
for($i=1;$kolko[0]-($limit*$i)>0;++$i)$out.="<a href='?page=admin&what=$what&pag=$i&mod=$mod&year=$year&month=$month&day=$day'>[$i]</a>\n";
if($kolko[0]-($limit*($i-1))+1>0&&$i!==1)$out.="<a href='?page=admin&what=$what&pag=$i&mod=$mod&year=$year&month=$month&day=$day'>[$i]</a>\n";
if(isset($endp))$out.="</p>";};





if ($mod == 'weekly') {
	$_SESSION['day'] = implode ('-', array ($year, $month, $day));
	$out .= '<br /><br />
	<h2>' . $translate['stats.weekly'] . '</h2>
	<img src="admin/images/stats-weekly.php" alt="" />'; 
};





if ($mod == 'monthly') {
	$_SESSION['day'] = implode ('-', array ($month, $year));
	$out .= '<br /><br />
	<h2>' . $translate['stats.monthly'] . '</h2>
	<img src="admin/images/stats-monthly.php" alt="" />'; 
};






if ($mod == 'yearly') {
	$_SESSION['day'] = $year;
	$out .= '<br /><br />
	<h2>' . $translate['stats.yearly'] . '</h2>
	<img src="admin/images/stats-yearly.php" alt="" />'; 
};
?>
