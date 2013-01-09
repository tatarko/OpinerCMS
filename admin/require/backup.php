<?php

// Kontrola jadra
if (!defined ('in')) exit ();
$out .= '<p class="right">
 <a href="?what=file-manager">' . TempIcon ('cat') . ' ' . $translate['fman'] . '</a>
 <a href="?what=file-manager&mod=download-manager">' . TempIcon ('cat') . ' ' . $translate['fman.down'] . '</a>
 <a href="?what=statistics">' . TempIcon ('cat') . ' ' . $translate['stats'] . '</a>
 <a href="?what=backup">' . TempIcon ('cat') . ' ' . $translate['backup'] . '</a>
 <a href="?what=labs">' . TempIcon ('cat') . ' ' . $translate['labsprojects'] . '</a>
</p>'.n;

// Pridanie štýlov do hlavičky
$tohead[] = '<link rel="stylesheet" href="admin/remote/css/backup.css" type="text/css" />';

// Získanie výpisu tabuliek
$sql = mysql_query ("SHOW TABLES LIKE '{$prefix}_%'");
while ($table = mysql_fetch_row ($sql)) $tables[] = $table[0];

// Priečinky na archváciu
$dirs = array ('store/gallery', 'store/icons', array ('store/files'), 'languages', array ('templates'), 'admin/apps', 'admin/hcm', 'admin/remote/schemas');
if (array_search ($_CONFIG['wysiwyg'], array ('texyla', 'none')) === false) $dirs[] = array ('codes/' . $_CONFIG['wysiwyg']);
if (array_search ($_CONFIG['imgbrowser'], array ('shadowbox', 'pavio')) === false) $dirs[] = array ('codes/' . $_CONFIG['imgbrowser']);

// Nasadenie nadpisu
$out = HeadIfPost ($translate['backup']);



/*--- Export systému ---*/

if (isset ($_REQUEST['export']))
{

	// Ak existuje nejaký archív, tak ho zmaže
	@unlink('backup.zip');
	
	// Pokúsi sa načítať oficiálny archív z netu
	$todrop = @copy ('http://feedback.tatarko.sk/versions/' . in . '.zip', 'backup.zip') ? true : false;
	
	// Otvorí archív na zápis
	if (class_exists ('ZipArchive')
	and $zip = new ZipArchive
	and false !== $zip -> open ('backup.zip', ZipArchive::CREATE)) {
	
		// Ak treba dať preč veci ohľadom inštalácie
	        if ($todrop) {
			$zip -> deleteName ('default.htaccess');
			$zip -> deleteName ('install.php');
			$zip -> deleteName ('readme.txt');
		};

		// Pridanie základného súborového obsahu
		foreach ($dirs as $dir)
		if (is_array ($dir))
		addDir2Zip ($dir[0], $zip, true);
		else addDir2Zip ($dir, $zip);
		$zip -> addFile ('_config.php', '_config.php');
		
		// Pridanie špeciálnych súborov na základe stavu systému
		if (file_exists ('.htaccess')) $zip -> addFile ('.htaccess', '.htaccess');
		if (!empty ($_CONFIG['favicon'])) $zip -> addFile ('media/' . $_CONFIG['favicon'], 'media/' . $_CONFIG['favicon']);

		// Zálohovanie dátabazy
		$i = 0;
		foreach ($tables as $table) {
			$result = mysql_fetch_row (mysql_query ('SHOW CREATE TABLE `' . $table . '`'));
			$queries[$i++] = $result[1];

			$sql = mysql_query ('SELECT * FROM `' . $table . '`');
			$int = 0;
			while ($data = mysql_fetch_row ($sql)){
				if ($int++ % 5000 == 0)
				$queries[$i++] = "INSERT INTO `{$table}` VALUES\n('" . implode ("', '", $data) . "')";
				else $queries[($i-1)] .= ",\n" . "('" . implode ("', '", $data) . "')";
			};


		};
		$info = explode ('~$~', SystemInfo);
		$zip -> addFromString ('_backup.php', '<?php
if (!defined ("in")) exit ();

$about = array (
	"generator" => "' . $info[0] . ' ' . $info[1] . '",
	"timestamp" => ' . time() . ',
	"tspk" => "' . in . '",
);

$queries = ' . var_export ($queries, true) . ';
?>');



		// Uloženie archívu
		$zip -> close();
		$out .= getIcon ('info', langrep ('backup.export.success', '<a href="backup.zip">backup.zip</a>'));
	} else $out .= getIcon ('error', $translate['backup.zip.error']);
};










/*--- Odinštalácia systému systému ---*/

if (isset ($_REQUEST['uninstall'])){
	uninstallSystem($tables);
	$out .= getIcon ('info', $translate['successact']);
}










/*--- Obnova zálohy ---*/

if (isset ($_REQUEST['import'])) {
	if (file_exists ('backup.zip')) {
		if (class_exists ('ZipArchive')
		and $zip = new ZipArchive
		and false !== $zip -> open ('backup.zip')
		and uninstallSystem($tables)) {

			// Obnoví archív
			$zip -> extractTo ('./');
		        $zip -> close ();
		        
			// Vloží údaje do databázy
			include ('_backup.php');
			foreach ($queries as $query)
			mysql_query ($query);
			
			$out .= getIcon ('info', $translate['successact']);
		} else $out .= getIcon ('error', $translate['backup.zip.error']);
	} else $out .= getIcon ('error', langrep ('backup.import.nozip', 'backup.zip'));
}










/*--- Reinštalácia systému ---*/

if (isset ($_REQUEST['reinstall'])) {
	if (class_exists ('ZipArchive')
	and $zip = new ZipArchive
	and false !== $zip -> open ('http://opiner.tatarko.sk/media/download.php?file=34')
	and uninstallSystem($tables)) {
		$zip -> extractTo ('./');
	        $zip -> close ();
		Header ('Location: install.php');
	} else $out .= getIcon ('error', $translate['backup.zip.error']);
}










/*--- Základný výstup ---*/

$out .= '<ul id="backup">
 <li>
  <a href="admin.php?what=' . $what . '&export">
   <img src="admin/images/backup-export.png" alt="' . $translate['backup.export'] . '" />
   <span>' . $translate['backup.export'] . '</span>
  </a>
 </li>
 <li>
  <a href="admin.php?what=' . $what . '&import">
   <img src="admin/images/backup-import.png" alt="' . $translate['backup.import'] . '" />
   <span>' . $translate['backup.import'] . '</span>
  </a>
 </li>
 <li>
  <a href="admin.php?what=' . $what . '&reinstall">
   <img src="admin/images/backup-reinstall.png" alt="' . $translate['backup.reinstall'] . '" />
   <span>' . $translate['backup.reinstall'] . '</span>
  </a>
 </li>
 <li class="last">
  <a href="admin.php?what=' . $what . '&uninstall">
   <img src="admin/images/backup-uninstall.png" alt="' . $translate['backup.uninstall'] . '" />
   <span>' . $translate['backup.uninstall'] . '</span>
  </a>
 </li>
</ul>
<p id="notice">' . langrep ('backup.old', '<a href="admin.php?what=database">' . $translate ['dbman'] . '</a>') . '</p>';
?>