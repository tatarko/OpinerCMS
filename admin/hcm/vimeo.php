<?php

if (!isset ($param1) or empty ($param1)) return '';
$param1 = explode ('.com/', $param1);
if (count ($param1) < 2) return '';
$param1 = $param1[1];
$out = '<object width="514" height="388"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' . $param1 . '&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=' . $param1 . '&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="514" height="388"></embed></object>';

?>