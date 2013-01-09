<?php
if (isset ($_REQUEST['file']) and !empty ($_REQUEST['file']))
{
	include ('../../media/get-config.php');
	list ($name, $path) = @mysql_fetch_row (@mysql_query ("SELECT `fname`, `path` FROM `{$prefix}_appfiles_files` WHERE `id` = " . intval ($_REQUEST['file']) . " LIMIT 1"));
	Header ('Content-Description: File Transfer');
	Header ('Content-Type: application/force-download');
	Header ('Content-Disposition: attachment; filename="' . $name . '"');
	readfile ('../../' . $path);
}
else
{
class filesapp extends plugin {

	protected function load ()
	{
	        global $_CONFIG, $prefix;
		$this -> title = 'Súbory';
		$this -> version = '1.0';
		$this -> author = 'Tomáš Tatarko';
		$this -> url = 'http://tatarko.sk/';
		$this -> description = 'Nahrávanie súborov a obrázkov na stránku.';
		$this -> modes = array ('application', 'widget', 'staticrun');
		$this -> cache = true;
		$this -> redactors = true;
		$this -> values = array (
		);
		$this -> tables = array (
			'files'	=> '`id` bigint unsigned NOT NULL AUTO_INCREMENT, `fname` tinytext NOT NULL, `path` text NOT NULL, PRIMARY KEY (`id`)',
		);
	}
	
	protected function application ()
	{
	        if (isset ($_FILES['file']) and $_FILES['file']['error'] == 0)
	        {
	                global $translate;
	                $hash = sha1(time());
			if (move_uploaded_file ($_FILES['file']['tmp_name'], 'store/files/' . $hash)
			and $this -> query ("INSERT INTO `{prefix}files` (`fname`, `path`) VALUES ('" . adjust ($_FILES['file']['name']) . "', 'store/files/$hash');"))
			$return = getIcon ('info', $translate['successadd'] . '<br />Text na pridanie do sekcie/článku: <strong>#file:' . mysql_insert_id() . '#</strong>');
			else $return = getIcon ('error', $translate['failureadd']);
		}
		else $return = '';

	       	$return .= '<form action="admin.php?app=' . $this -> apphash . '" method="post" enctype="multipart/form-data">
 <label for="filesFile">Súbor:</label>
 <input type="file" required="required" name="file" id="filesFile" /><br />
 <input type="submit" value="Nahrať" />
</form>'.n;

		$sql = $this -> query ("SELECT `id`, `fname` FROM `{prefix}files` ORDER BY `id` DESC");
		$return .= '<table id="admintable" cellspacing="3px"><tr><th>Názov súboru</th><th>Kód</th></tr>'.n;
		while ($row = mysql_fetch_assoc ($sql))
		$return .= '<tr><td>' . $row['fname'] . '</td><td>#file:' . $row['id'] . '#</td></tr>'.n;
		return $return . '</table>';
	}

	protected function widget ()
	{
	        return '<form action="admin.php?app=' . $this -> apphash . '" method="post" enctype="multipart/form-data">
 <label for="filesFile">Súbor:</label>
 <input type="file" required="required" name="file" id="filesFile" /><br />
 <input type="submit" value="Nahrať" />
</form>'.n;
	}

	protected function staticrun ()
	{
	        global $out;
	        $pos = 0;
		while (preg_match ('#\#file\:([0-9]*)\##', $out, $match, PREG_OFFSET_CAPTURE, $pos))
		{
			$pos = $match[0][1] + 1;
			if ($data = mysql_fetch_assoc ($this -> query ("SELECT * FROM `{prefix}files` WHERE `id` = {$match[1][0]} LIMIT 1")))
			$out = str_replace ($match[0][0], '<span class="download"><a href="admin/apps/files.php?file=' . $data['id'] . '">' . $data['fname'] . '</a></span>', $out);
			else $out = str_replace ($match[0][0], '', $out);
		}
	}
};
}
?>