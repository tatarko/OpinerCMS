<?php

if (!defined ('in')) exit ();
$TempConfig = array (



        // Informácie o motívoch
        'info' => array (
                'author'        => 'Mirk Belly',
                'link'          => 'http://www.cheworld.sk',
                'spv'           => '3',
                'count-menu'    => '1',
        ),



        // Farby motívu
        'theme-colors' => array (
                'ap1'   => '#fff',
                'ap2'   => '#e0e9ed',
                'ap3'   => '#fff',
                'ap4'   => '#444',
        ),



        // Menu
        'menu1' => array (
        'type'          => 'bar',
        'menu-start'    => '',
        'menu-end'      => '',
        'list-start'    => '<ul id="menu">',
        'list-end'      => '</ul>',
        'link-start'    => ' <li>',
        'link-end'      => '</li>',
),
        
        // Postranné menu
        'menu2' => array (
        'type'          => 'boxes',
        'menu-start'    => '',
        'menu-end'      => '',
        'box-start'     => '<div%ID%>',
        'box-end'       => '</div>',
        'box-head'      => '<h3>%TITLE%</h3>',
        'box-search'    => '',
        'list-start'    => '<ul>',
        'list-end'      => '</ul>',
        'link-start'    => '<li>',
        'link-end'      => '</li>',
        # 'link-first'  => '',
),


        // Zoznamy článkov
        'arts' => array (
                'table' => ' <!-- Post -->
 <div class="post">
  <h2>[head]</h2>
  <div class="meta">%d. %F, %Y | [categories]</div>
  [content]
 </div>',
                'author'		=> '<span class="author">%</span>',
                'comments'		=> '<span class="comments">%</span>',
                'rating'		=> '<span class="rating">%</span>',
                'reads'			=> '<span class="reads">%</span>',
	),

	// Zoznamy komentárov
        'comments' => array (
                'table' => ' <!-- Komentár -->
 <div class="comment">
  <img src="[gravatar]" alt="Gravatar" class="gravatar" />
  <h4>[author]</h4>
  <div class="content">[content]</div>
  <div class="meta">[datetime] | [reply][admin]</div>
 </div>',
                'admin' => ' | %',
                'pairclass' => '',
                'classwriter' => '',
	),
);
?>