<?php

class systemstatsapp extends plugin {

	protected function load () {
		$this -> title = 'Štatistiky systému';
		$this -> version = '1.1';
		$this -> author = 'Ovalio';
		$this -> url = 'http://opiner.tatarko.sk/';
		$this -> description = 'Štatistiky o používaní systému Opiner CMS na Vašej stránke v prehľadnej forme.';
		$this -> modes = array ('application');
		$this -> cache = false;
		$this -> redactors = false;
		$this -> values = array ();
		$this -> tables = array ();
	}
	
	protected function application () {
		global $prefix, $_CONFIG;
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_clanky"));
		$querys[] = $query[0]; #0
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_comments"));
		$querys[] = $query[0]; #1
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_gall"));
		$querys[] = $query[0]; #2
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_img"));
		$querys[] = $query[0]; #3
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_cats"));
		$querys[] = $query[0]; #4
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_cats WHERE showing = 1"));
		$querys[] = $query[0]; #5
		$querys[] = ($querys[4] == 0) ? 0 : round ($querys[5] / $querys[4] * 100, 0); #6
		$query = @mysql_fetch_row (@mysql_query ("SELECT id FROM {$prefix}_clanky ORDER BY id DESC LIMIT 1"));
		$querys[] = ($query[0] == '') ? 0 : $query[0]; #7
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_clanky WHERE showing = 1 AND `confirmed` = 1 AND `added` <= NOW()"));
		$querys[] = $query[0]; #8
		$querys[] = ($querys[0] == '0') ? 0 : round ($querys[8] / $querys[0] * 100, 0); #9
		$querys[] = ($querys[4] == '0') ? 0 : round ($querys[0] / $querys[4], 1); #10
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_gall WHERE showing = 1"));
		$querys[] = $query[0]; #11
		$querys[] = ($querys[2] == '0') ? 0 : round ($querys[11] / $querys[2] * 100, 0); #12
		$querys[] = ($querys[2] == '0') ? 0 : round ($querys[3] / $querys[2], 1); #13
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_img WHERE nadpis != ''"));
		$querys[] = $query[0]; #14
		$querys[] = ($querys[3] == '0') ? 0 : round ($querys[14] / $querys[3] * 100, 0); #15
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_img WHERE popis != ''"));
		$querys[] = $query[0]; #16
		$querys[] = ($querys[3] == '0') ? 0 : round ($querys[16] / $querys[3] * 100, 0); #17
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_menu"));
		$querys[] = $query[0]; #18
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_links"));
		$querys[] = $query[0]; #19
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_download"));
		$querys[] = $query[0]; #20
		$query = @mysql_fetch_row (@mysql_query ("SELECT SUM(hits) FROM {$prefix}_download"));
		$querys[] = ($query[0] == '') ? 0 : $query[0]; #21
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_sec"));
		$querys[] = $query[0]; #22
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_sec WHERE msec = 0 OR id = '{$_CONFIG['homepage']}'"));
		$querys[] = ($query[0] == '') ? 0 : $query[0]; #23
		$querys[] = ($querys[22] == '0') ? 0 : round ($querys[23] / $querys[22] * 100, 0); #24
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_hits"));
		$querys[] = $query[0]; #25
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM {$prefix}_visits"));
		$querys[] = $query[0]; #26
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(DISTINCT ip) FROM {$prefix}_hits"));
		$querys[] = $query[0]; #27
		$query = explode ('~$~', SystemInfo);
		include ('codes/' . $_CONFIG['imgbrowser'] . '/browser.php');
		$querys[] = ''; #28
		$querys[] = ''; #29
		$querys[] = ''; #30
		$querys[] = $query[0]; #31
		$querys[] = $query[1]; #32
		$querys[] = $query[2]; #33
		$querys[] = $query[3]; #34
		$querys[] = in; #35
		$querys[] = ($querys[20] == '0') ? 0 : round ($querys[21]/$querys[20]); #36
		$querys[] = '';
		
		$query = @mysql_fetch_row (@mysql_query ("SELECT SUM(`reads`) FROM {$prefix}_clanky"));
		$querys[] = $query[0]; #38
		$querys[] = ($querys[0] == '0') ? 0 : round ($querys[38] / $querys[0]); 	#39
		
		// Vek webu
		$query = @mysql_fetch_row (@mysql_query ("SELECT UNIX_TIMESTAMP(`kedy`) FROM `{$prefix}_hits` ORDER BY `id` ASC"));
		$time = time() - $query[0];
		$webAge = $time;
		$days = floor ($time / 86400);
		$time -= $days*86400;
		$querys[] = "$days dní";							#40
		if ($days > 360) {
			$years = floor ($days/360);
			$querys[40] = str_replace ("$days dní", "$years rok(ov), ".($days - $years*360)." dní", $querys[40]);
			$days -= $years*360;
		};
		if ($days > 30) {
			$mon = floor ($days/30);
			$querys[40] = str_replace ("$days dní", "$mon mesiac(ov), ".($days - $mon*30)." dní", $querys[40]);
		};
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM `{$prefix}_comments` WHERE `kde` LIKE 'clanok_%'"));
		$querys[] = $query[0]; 								#41
		$querys[] = ($querys[0] == '0') ? 0 : round ($querys[41] / $querys[0], 1); 	#42
		$query = @mysql_fetch_row (@mysql_query ("SELECT COUNT(*) FROM `{$prefix}_iplog` WHERE `what` = 'rss'"));
		$querys[] = $query[0]; 								#43
		
		
		
		
		
		
		/*--- Veci s vekom webu ---*/
		
		$stats = array (
			round ($querys[0] / ($webAge / 86400), 1),				# 0
			round ($querys[38] / ($webAge / 86400)),				# 1
			round ($querys[3] / ($webAge / 86400), 1),				# 2
			round ($querys[21] / ($webAge / 86400)),				# 3
			round ($querys[25] / ($webAge / 86400)),				# 4
			round ($querys[26] / ($webAge / 86400)),				# 5
			round ($querys[1] / ($webAge / 86400), 1),				# 6
		);
		
		$mysqlver = @mysql_get_server_info();
			if ($mysqlver != null and mb_substr_count ($mysqlver, '-') != 0)
			$mysqlver = mb_substr ($mysqlver, 0, strpos($mysqlver, '-'));
		
		
		
		global $tohead;
		$tohead[] = '<style>
	.table td {padding: 2px 12px; vertical-align: top;}
</style>';
		return "<img src='http://feedback.tatarko.sk/Angelina.png' width='512px' height='128px' />
		<h2>Systém</h2>
		<table class='table'>
			<tr><td>Redakčný systém:</td><td><strong>$querys[31] $querys[32]</strong> <em>od autora</em> <strong><a href='$querys[34]'>$querys[33]</a></strong></td></tr>
			<tr><td>TSPK:</td><td><strong>$querys[35]</strong></td></tr>
			<tr><td>Licenčné číslo:</td><td><strong>"._SerialNumber."</strong></td></tr>
			<tr><td>Verzia PHP:</td><td><strong>".PHP_VERSION."</strong></td></tr>
			<tr><td>Verzia MySQL:</td><td><strong>$mysqlver</strong></td></tr>
			<tr><td>Adresa:</td><td><strong><a href='"._SiteLink."' target='_blank'>" . _SiteLink . "</a></strong></td></tr>
			<tr><td>Vek webu:</td><td><strong>$querys[40]</td></tr>
		</table>
		
		<h2>Databáza</h2>
		<table width='100%'><tr><td valign='top' width='40%'>
		<table class='table'>
			<tr><td rowspan='2'>Sekcie:</td><td><b>$querys[22]</b> pridaných</td></tr>
			<tr><td><b>$querys[23]</b> sa zobrazuje ($querys[24]%)</td></tr>
			<tr><td rowspan='2'>Kategórie:</td><td><b>$querys[4]</b> pridaných</td></tr>
			<tt><td><b>$querys[5]</b> sa zobrazuje ($querys[6]%)</td></tr>
			<tr><td rowspan='6'>Články:</td><td><b>$querys[0]</b> pridaných</td></tr>
			<tr><td><b>$querys[8]</b> sa zobrazuje ($querys[9]%)</td></tr>
			<tr><td><b>$querys[38]x</b> prečítané ($querys[39]/článok, $stats[1]/deň)</td></tr>
			<tr><td><b>$querys[41]x</b> komentované ($querys[42]/článok)</td></tr>
			<tr><td><b>$querys[10]</b> priemerne na kategóriu</td></tr>
			<tr><td><b>$stats[0]</b> priemerne pridaných každý deň</td></tr>
			<tr><td rowspan='2'>Súbory:</td><td><b>$querys[20]</b> pridaných</td></tr>
			<tr><td><b>$querys[21]x</b> stiahnuté ($querys[36]/súbor, $stats[3]/deň)</td></tr>
		</table>
		</td><td valign='top'>
		<table class='table'>
			<tr><td rowspan='2'>Albumy:</td><td><b>$querys[2]</b> pridaných</td></tr>
			<tr><td><b>$querys[11]</b> sa zobrazuje ($querys[12]%)</td></tr>
			<tr><td rowspan='5'>Média:</td><td><b>$querys[3]</b> pridaných</td></tr>
			<tr><td><b>$querys[14]</b> pomenované ($querys[15]%)</td></tr>
			<tr><td><b>$querys[16]</b> s popisom ($querys[17]%)</td></tr>
			<tr><td><b>$querys[13]</b> priemerne na album</td></tr>
			<tr><td><b>$stats[2]</b> priemerne na deň</td></tr>
			<tr><td>Zobrazenia stránky:</td><td><b>$querys[25]</b> ($stats[4]/deň)</td></tr>
			<tr><td>Návštevy:</td><td><b>$querys[26]</b> ($stats[5]/deň)</td></tr>
			<tr><td>UIP:</td><td><b>$querys[27]</b></td></tr>
			<tr><td>Odberatelia RSS:</td><td><b>$querys[43]</b></td></tr>
		</table>
		</td></tr></table>";
	}
};