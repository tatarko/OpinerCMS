<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $translate['info.short'];?>">
<head>
 <title>Opiner CMS &raquo; <?php echo $translate['controlpanel'];?></title>
 <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
 <meta name="robots" content="noindex,nofollow" />
 <link rel="stylesheet" href="admin/remote/css/default.css" type="text/css" />
 <link rel="shortcut icon" href="admin/images/favicon.png" type="image/png" /> 
 <script src="languages/<?php echo $_CONFIG['language'];?>.php" type="text/javascript" charset="utf-8"></script>
 <script src="admin/remote/js/jquery.js" type="text/javascript" charset="utf-8"></script>
 <script src="admin/remote/js/default.js" type="text/javascript" charset="utf-8"></script>
 <?php echo $tohead; ?>
</head>
<body onload="loadAdminApi(<?php echo $pl != '' ? 1 : 0; ?>, <?php echo (isset($_CONFIG['labs.ajaxadmin']) and $_CONFIG['labs.ajaxadmin'] == 1) ? 1 : 0; ?>)">
	
<!-- Obsah -->
<?php if (!ajaxmode) {?>
<div id="panel">
 <div id="righter">
  <?php echo NAME; ?>
  <a class="ajax" href="admin.php?what=account" title="<?php echo $translate['account.title'];?>"><img src="admin/images/mAccount.png" alt="<?php echo $translate['account.title'];?>"></a>
  <a href="<?php echo _SiteLink;?>" title="<?php echo $translate['gosite'];?>"><img src="admin/images/mSite.png" alt="<?php echo $translate['gosite'];?>"></a>
  <a href="admin/remote/manual.html" title="<?php echo $translate['manual'];?>" target="_blank"><img src="admin/images/mManual.png" alt="<?php echo $translate['manual'];?>"></a>
  <a href="login.php?out" title="<?php echo $translate['logout'];?>"><img src="admin/images/mLogout.png" alt="<?php echo $translate['logout'];?>"></a>
 </div>
 <div id="lefter"><a href="./"><?php echo $_CONFIG['title'];?></a> &raquo; <a class="ajax" href="admin.php"><?php echo $translate['controlpanel'];?></a><?php echo $MAINHEADING;?></div>
 <div class="cleaner"></div>
</div>
<?php }; ?>
<div id="page">
<?php if (!ajaxmode) { ?>
 <div id="menu">
  <ul>
   <li id="mAdmin"><a href="admin.php"><img src="admin/images/mAdmin.png" alt="" /></a></li>
   <?php if (ADMIN) { ?><li id="mMenu"><a href="admin.php?what=menu"><img src="admin/images/mMenu.png" alt="" /></a>
    <ul>
     <li><a class="ajax" href="?what=menu"><?php echo $translate['menu']; ?></a></li>
     <li><a class="ajax" href="?what=sections"><?php echo $translate['sections']; ?></a></li>
     <li><a class="ajax" href="?what=art-categories"><?php echo $translate['categories']; ?></a></li>
     <li><a class="ajax" href="?what=links"><?php echo $translate['links']; ?></a></li>
    </ul>
   </li>
   <?php } elseif (!ADMIN and isset ($allowedsections)) { ?><li id="mMenu"><a href="admin.php?what=sections"><img src="admin/images/mMenu.png" alt="" /></a></li>
   <?php }; ?><li id="mContent"><a href="?what=<?php echo $_USER_INFO['articles'] ? "articles" : ($_USER_INFO['albums'] ? "gallery" : "polls"); ?>"><img src="admin/images/mContent.png" alt="" /></a>
    <ul>
<?php
if (isset ($allowedsections)) echo '     <li><a class="ajax" href="?what=sections">' . $translate['sections'] . '</a></li>'.n;
if (ADMIN or $_USER_INFO['articles']) echo '     <li><a class="ajax" href="?what=articles">' . $translate['articles'] . '</a></li>'.n;
if (ADMIN or $_USER_INFO['albums']) echo '     <li><a href="?what=gallery">' . $translate['albums'] . '</a></li>'.n;
?>
     <li><a class="ajax" href="?what=polls"><?php echo $translate['polls'];?></a></li>
    </ul>
   </li>
<?php
$sql = @mysql_query ("SELECT SHA1(CONCAT(`id`, `fname`, `installed`)) as `hash`, `title` FROM `{$prefix}_apps` WHERE `allowed` = 1 AND `application` = 1" . ((ADMIN)?'':' AND `redactors` = 1') . " ORDER BY `title` ASC");
if (ADMIN or @mysql_num_rows ($sql) > 0) echo '    <li id="mApps"><a href="?what=' . (ADMIN ? 'market' : 'apps') . '"><img src="admin/images/mApps.png" alt="" /></a>
    <ul>' . n;
if (ADMIN) echo '     <li><em><a class="ajax" href="?what=market">' . $translate['apps.market'] . '</a></em></li>'.n;
while ($app = @mysql_fetch_assoc ($sql)) echo '    <li><a class="ajax" href="admin.php?app=' . $app['hash'] . '">' . $app['title'] . '</a></li>' .n;
if (ADMIN or @mysql_num_rows ($sql) > 0) echo '   </ul>
    </li>' . n;
if (ADMIN) echo '   <li id="mUsers"><a href="?what=redactors"><img src="admin/images/mUsers.png" alt="" /></a>
    <ul>
     <li><a class="ajax" href="?what=redactors">' . $translate['redactors'] . '</a></li>
     <li><a class="ajax" href="?what=massmail">' . $translate['redactors.massmail'] . '</a></li>
     <li><a class="ajax" href="?what=confirm">' . $translate['redactors.confirm'] . '</a></li>
    </ul>
   </li>
   <li id="mSettings"><a href="admin.php?what=options&mod=site-info"><img src="admin/images/mSettings.png" alt="" /></a>
    <ul>
     <li><a class="ajax" href="admin.php?what=options&mod=site-info">' . $translate['settings.site'] . '</a></li>
     <li><a class="ajax" href="admin.php?what=options&mod=functions">' . $translate['settings.functions'] . '</a></li>
     <li><a class="ajax" href="admin.php?what=options&mod=limits">' . $translate['settings.limits'] . '</a></li>
     <li><a class="ajax" href="admin.php?what=options&mod=admin">' . $translate['controlpanel'] . '</a></li>
     <li><a class="ajax" href="?what=microblog">' . $_CONFIG['microblog_head'] . '</a></li>
     <li><a class="ajax" href="admin.php?what=options&mod=connect">' . $translate['settings.database'] . '</a></li>
    </ul>
   </li>
   <li id="mSystem"><a href="?what=file-manager"><img src="admin/images/mSystem.png" alt="" /></a>
    <ul>
     <li><a href="?what=file-manager">' . $translate['fman'] . '</a></li>
     <li><a class="ajax" href="?what=file-manager&mod=download-manager">' . $translate['fman.down'] . '</a></li>
     <li><a class="ajax" href="?what=statistics">' . $translate['stats'] . '</a></li>
     <li><a class="ajax" href="?what=backup">' . $translate['backup'] . '</a></li>
     <li><a class="ajax" href="?what=labs">' . $translate['labsprojects'] . '</a></li>
    </ul>
   </li>' . n; ?>
  </ul>
  <?php $info = explode ('~$~', SystemInfo); echo '<!-- p>' . date('Y') . ' &copy; ' . langrep ('systemby', '<a href="' . $info[3] . '" target="_blank">' . $info[0] . ' ' . $info[1] . '</a>', '<strong>' . $info[2] . '</strong>') . '</p -->'; ?>
<?php
if (!isset ($cache['checkupdates']) or $cache['checkupdates'] <= time() - 86400) {
	$query = @mysql_query ("SELECT `fname`, `version` FROM `{$prefix}_apps`");
	while ($info = @mysql_fetch_row ($query)) $uapps[] = $info[0] . '-' . $info[1];
	if (isset ($uapps) and $uapps = implode ('~', $uapps)
	and false !== ($xml = @simplexml_load_file ('http://friends.tatarko.sk/cofr.php?updatelist=' . $uapps . '&site=' . _SiteLink))
	and isset ($xml['protocol'], $xml -> update) and $xml['protocol'] == '1.0') {
		echo '  <h4>&nbsp; ' . $translate['apps.updates'] . "</h4>\n  <p>\n";
		foreach ($xml -> update as $update)
		echo '   <a class="ajax" href="?what=market&amp;update=' . $update -> fname . '&amp;url=' . $update -> url . '">&nbsp; ' . $update -> title . ' ' . $update -> newversion . "</a><br />\n";
		echo "  </p>\n";
	};
	$cache['checkupdates'] = time();
};
?>
 </div>
<?php
};
echo $pl;
?>
 <div id="ajaxloader">
 <div id="content">
  <?php echo $out; ?>
 </div>
 </div>
</div>
<div class="cleaner">&nbsp;</div>
<?php
if (isset ($_CONFIG['labs.statsontheuse']) and $_CONFIG['labs.statsontheuse'] == 1) echo '<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(["_setAccount", "UA-15781502-4"]);
  _gaq.push(["_setDomainName", "none"]);
  _gaq.push(["_setAllowLinker", true]);
  _gaq.push(["_trackPageview"]);

  (function() {
    var ga = document.createElement("script");
    ga.type = "text/javascript";
    ga.async = true;
    ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(ga, s);
  })();

</script>';
?>
</body>
</html>