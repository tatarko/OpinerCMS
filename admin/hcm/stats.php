<?php
$today = date('d-m-Y');
$result = mysql_query("SELECT COUNT(id) FROM {$prefix}_hits UNION
SELECT COUNT(id) FROM {$prefix}_hits WHERE kedy>=SUBDATE(NOW(),INTERVAL 1 WEEK) UNION
SELECT COUNT(id) FROM {$prefix}_hits WHERE DATE_FORMAT(kedy,'%d-%m-%Y')='$today' UNION
SELECT COUNT(id) FROM {$prefix}_visits UNION
SELECT COUNT(id) FROM {$prefix}_visits WHERE kedy>=SUBDATE(NOW(),INTERVAL 1 WEEK) UNION
SELECT COUNT(id) FROM {$prefix}_visits WHERE DATE_FORMAT(kedy,'%d-%m-%Y')='$today' UNION
SELECT COUNT(DISTINCT ip) FROM {$prefix}_hits WHERE kedy>=SUBDATE(NOW(),INTERVAL 15 MINUTE) UNION
SELECT COUNT(DISTINCT ip) FROM {$prefix}_hits WHERE DATE_FORMAT(kedy,'%d-%m-%Y')='$today'");
while($row = mysql_fetch_row($result)){$stats[] = number_format($row[0], 0, '', ' ');}
$LineBreaker = (isset ($param1) and $param1 == '0') ? ', ' : "<br />\n";
$out = ($LineBreaker != ', ') ? '<p>' : '';
$out .= '<strong>' . $translate['stats.today'] . ':</strong> <acronym title="' . $translate['stats.uip'] . '" style="border:none;">' . $stats[7] . '</acronym>/<acronym title="' . $translate['stats.visits'] . '" style="border:none;">' . $stats[5] . '</acronym>/<acronym title="' . $translate['stats.hits'] . '" style="border:none;">' . $stats[2] . '</acronym>' . $LineBreaker . 
'<strong>' . $translate['stats.week'] . ':</strong> <acronym title="' . $translate['stats.visits'] . '" style="border:none;">' . $stats[4] . '</acronym>/<acronym title="' . $translate['stats.hits'] . '" style="border:none;">' . $stats[1] . '</acronym>' . $LineBreaker .
'<strong>' . $translate['stats.all'] . ':</strong> <acronym title="' . $translate['stats.visits'] . '" style="border:none;">' . $stats[3] . '</acronym>/<acronym title="' . $translate['stats.hits'] . '" style="border:none;">' . $stats[0] . '</acronym>' . $LineBreaker . 
'<strong>' . $translate['stats.online'] . ':</strong> <acronym title="' . $translate['stats.online.title'] . '" style="border:none;">' . $stats[6] . '</acronym>' . (($LineBreaker != ', ') ? '</p>'.n : '');
?>