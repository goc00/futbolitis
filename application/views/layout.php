<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<title>Futbolitis</title>

		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/futbolitisv1.css" />
		<link rel="Stylesheet" type="text/css" href="<?= base_url() ?>assets/css/custom-theme/jquery-ui-1.8.16.custom.css" />

		<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery-1.7.min.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>own"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.easing.1.3.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/futbolitis-1.0.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/jquery.vticker.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/s3Slider.js"></script>
	</head>

	<body>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/es_LA/all.js#xfbml=1&appId=245147898888987";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
	
		<?php $this->load->view("includes/login_vista"); ?>

		
		<div id="contenedor_div">
		
			<!-- LOGO FUTBOLITIS -->
			<div id="logo_menu_div">
				<img width="960" height="200" src="<?= base_url()."assets/images/logo_con_fondo.jpg" ?>" border="0" />
			</div>
			
			<?php
				// Si viene el flag, muestra el menú interno (interfaz)
				if(isset($inside)) $this->load->view("includes/menu_interno");
			?>
			
			<!-- CONTENIDO PRINCIPAL  -->
			<div id="principal_div">
				<?php
					$this->load->view($vista);
				?>
			</div>

			<!-- PIE  -->
			<div id="pie_div">
				hola
			</div>
			
		</div>
	</body>
</html>