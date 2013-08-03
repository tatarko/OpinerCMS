<?php
if (!defined ('in')) exit ();

$tohead[] = '<style type="text/css">
</style>';
$out = HeadIfPost ($translate['home']);
$out .= '<p class="welcome">' . langrep ('home.note', '<a href="http://dokumentacia.cheworld.sk" target="_blank">' . $translate['manual'] . '</a>') . '</p>';
if (!isset ($_CONFIG['startpage'])) $_CONFIG['startpage'] = 'menu';

switch ($_CONFIG['startpage'])
{

	// Správa menu
	case 'menu':
	        include ('admin/require/menu.php');
	break;

	// Správa komentarov
	case 'coms':
	        include ('admin/require/menu.php');
	break;

	// Články
	case 'arts':
	        include ('admin/require/articles.php');
	break;

	// Albumy
	case 'albs':
	        include ('admin/require/gallery.php');
	break;

	// Zoznam aplikácií
	case 'apps':
		$out .= '
		<table id="homepage" cellspacing=7px>
			<tr>'.n;

		if (ADMIN or $_USER_INFO['articles']) $_PANELS[] = array ('what=articles', $translate['articles'], $translate['home.articles'], '&amp;mod=add');
		if (ADMIN or $_USER_INFO['albums']) $_PANELS[] = array ('what=gallery', $translate['albums'], $translate['home.albums'], '&amp;mod=add');
		if (ADMIN) $_PANELS[] = array ('what=file-manager', $translate['fman'], $translate['home.fman'], '&amp;mod=upload');
		if (ADMIN) $_PANELS[] = array ('what=sections', $translate['sections'], $translate['home.sections'], '&amp;mod=add');
		if (ADMIN or $_USER_INFO['categories']) $_PANELS[] = array ('what=art-categories', $translate['categories'], $translate['home.categories'], '&amp;mod=add');
		if (ADMIN) $_PANELS[] = array ('what=menu', $translate['menu'], $translate['home.menu'], '&amp;mod=add');
		if (ADMIN) $_PANELS[] = array ('what=redactors', $translate['redactors'], $translate['home.redactors'], '&amp;mod=add');
		$sql = @mysql_query ("SELECT SHA1(CONCAT(`id`, `fname`, `installed`)) as `hash`, `title`, `description` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `application` = 1 AND `homepage` = 1" . ((ADMIN)?'':' AND `redactors` = 1') . " ORDER BY `title` ASC");
		while ($info = @mysql_fetch_assoc ($sql)) $_PANELS[] = array ('app=' . $info['hash'], $info['title'], $info['description']);

		foreach ($_PANELS as $i => $panel) {
		$out .= '		<td>
					<h3><a href="admin.php?' . $panel[0] . '">' . $panel[1] . '</a>' . ((isset ($panel[3]))?' <a href="admin.php?' . $panel[0] . $panel[3] . '"><span>' . $translate['add'] . '</span></a>':'') . '</h3>
					<p>' . $panel[2] . '</p>
				</td>'.n;
			if (++$i % 4 == 0) $out .= "\t</tr>\n\t<tr>\n";
		};
		$out .= '	</tr>
		</table>'.n;
	break;

	// Načítanie aplikácie
	default:
		$app = OpinerAutoLoader::loadPlugin ($_CONFIG['startpage'], 'application');
		$out = $app -> run ();
	break;
}
?>