<?php

if (!defined ('in')) exit ();
$TempConfig = array (



        // Informácie o motívoch
        'info' => array (
                'author'        => 'Tomáš Tatarko',
                'link'          => 'http://tatarko.sk/',
                'spv'           => '3',
                'count-menu'    => '2',
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
        'list-start'    => '<ul>',
        'list-end'      => '</ul>',
        'link-start'    => '<li>',
        'link-end'      => '</li>',
        # 'link-first'  => '',
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
                'table' => '				
				<!-- Post -->
				<div id="post">
				<h1>[head]</h1>
				<div class="meta">
					<span class="date">Pridané %d. %F %Y</span>
					<span class="categories">[categories]</span>
					<span class="reads">[reads]</span>
					<span class="comments">[comments]</span>
				</div>
					<div class="content">
						[content]
					</div>
					<div class="cleaner"></div>	
				</div>',
                'author'		=> '<span class="author">%</span>',
                'comments'		=> '<span class="comments">%</span>',
                'rating'		=> '<span class="rating">%</span>',
                'reads'			=> '<span class="reads">%</span>',
	),

	// Zoznamy komentárov
        'comments' => array (
                'table' => '				
				<!-- Komentár -->
				<div class="post">
				<h3 class="comment">[author]</h3>
		        		<div class="content">
		        			<img src="[gravatar]" alt="Gravatar" class="gravatar" />
						[content]
						<div class="cleaner"> </div>
					</div>
					<div class="meta-comments">
						<span class="date">[datetime]</span>
						<span class="comments">[reply][admin]</span>
						<div class="cleaner"></div>
					</div>
				</div>',
                'admin' => ', %',
                'pairclass' => '',
                'classwriter' => '',
	),
);
?>