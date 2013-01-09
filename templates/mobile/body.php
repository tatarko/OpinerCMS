<?php
global $translate;
$this->head ();
?>
</head>
<body>
<a id="exitmobileview" href="<?php echo _SiteLink; ?>?mobileview=false"><?php echo $translate['exitmobileview']; ?></a>
<div id="sitename"><?php $this->title (); ?></div>
<?php $this->menu (1); ?>
<div id="content"><?php $this->content (); ?></div>
<?php $this->menu (2); ?>
</body>
</html>