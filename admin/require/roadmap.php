<?php
if (!defined ('in')) exit ();


/*--- Obsah ---*/
if (isset ($_REQUEST['content'])) {
	$out .= HeadIfPost ($translate['content']);
	$out .= '<ul>'.n;
	if (ADMIN) $out .= '	<li><a href="?what=menu">' . $translate['menu'] . '</a></li>
	<li><a href="?what=sections">' . $translate['sections'] . '</a></li>
	<li><a href="?what=links">' . $translate['links'] . '</a></li>'.n;
	if (ADMIN or $_USER_INFO['articles']) $out .= '	<li><a href="?what=articles">' . $translate['articles'] . '</a></li>'.n;
	if (ADMIN or $_USER_INFO['categories']) $out .= '	<li><a href="?what=art-categories">' . $translate['categories'] . '</a></li>'.n;
	if (ADMIN or $_USER_INFO['albums']) $out .= '	<li><a href="?what=gallery">' . $translate['albums'] . '</a></li>'.n;
	$out .= '	<li><a href="?what=polls">' . $translate['polls'] . '</a></li>
</ul>';


/*--- Užívatelia ---*/
} else if (isset ($_REQUEST['users']) and ADMIN) {
	$out .= HeadIfPost ($translate['redactors']);
	$out .= '<ul>
	<li><a href="?what=redactors">' . $translate['redactors'] . '</a></li>
	<li><a href="?what=massmail">' . $translate['redactors.massmail'] . '</a></li>
	<li><a href="?what=confirm">' . $translate['redactors.confirm'] . '</a></li>
</ul>'.n;


/*--- Nastavenia ---*/
} else if (isset ($_REQUEST['settings']) and ADMIN) {
	$out .= HeadIfPost ($translate['settings']);
	$out .= '<ul>
	<li><a href="admin.php?what=options&mod=site-info">' . $translate['settings.site'] . '</a></li>
	<li><a href="admin.php?what=options&mod=functions">' . $translate['settings.functions'] . '</a></li>
	<li><a href="admin.php?what=options&mod=limits">' . $translate['settings.limits'] . '</a></li>
	<li><a href="admin.php?what=options&mod=admin">' . $translate['controlpanel'] . '</a></li>
	<li><a href="?what=microblog">' . $_CONFIG['microblog_head'] . '</a></li>
	<li><a href="admin.php?what=options&mod=connect">' . $translate['settings.database'] . '</a></li>
</ul>'.n;


/*--- Systém ---*/
} else if (isset ($_REQUEST['system']) and ADMIN) {
	$out .= HeadIfPost ($translate['system']);
	$out .= '<ul>
	<li><a href="?what=file-manager">' . $translate['fman'] . '</a></li>
	<li><a href="?what=file-manager&mod=download-manager">' . $translate['fman.down'] . '</a></li>
	<li><a href="?what=database">' . $translate['dbman'] . '</a></li>
	<li><a href="?what=statistics">' . $translate['stats'] . '</a></li>
	<li><a href="?what=search">' . $translate['ovase'] . '</a></li>
</ul>'.n;
};
?>