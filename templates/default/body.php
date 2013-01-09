<?php $this->head (); ?>
  </head>
  <body>
  
    <!-- Container -->
  	<div id="container">
		
		<!-- Header -->
		<div id="header">
		
		<div id="name">
		    
		  <div id="logo">
        <h1><?php $this->title (); ?></h1>
      </div>
		
    
    </div>

		<div id="menu">
		
			<div class="menu">
				
				<?php $this->menu (1); ?>
		
			</div>
			
		</div>
		
		</div>
		
		<!-- Main Content -->
		<div id="main">
		
		  <div id="content">
		    
		    <?php $this->content (); ?>
				
			</div>
  		
      <!-- Sidebar -->	
		  <div id="sidebar">
				<?php $this->menu (2); ?>
		  </div>

		  <span class="clear">&nbsp;</span>
		  
      </div>
		     
		</div>
		    <div class="push"></div>
		<!-- Footer -->
		<div id="footer">
		  <div id="center">  
		    <?php $this->foot ();?>
		  </div>
		</div>
	  
  </body>
</html>
