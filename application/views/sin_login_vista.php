
	
	<div class="noticiaCont bordesCaja">
		<div class="noticiaImag">
			<img src="<?= base_url() . $noticia_destacada->ruta_imagen; ?>" width="920" height="230" border="0" />
		</div>
		<div class="noticiaCaja">
			<div class="titulo"><?= $noticia_destacada->titulo; ?></div>
			<div class="cuerpo"><?= $noticia_destacada->contenido; ?></div>
		</div>
		<div class="noticiaCajaFondo trans"></div>
	</div>
	
	<div id="contenidoCentro">
		<div class="izquierda">

			<div id="cajaEstad" class="bordesCaja">
				<div class="gradiente-verde-negro">Estad&iacute;sticas</div>
				
				<div class="estadCont">
					<div class="estadLabel">Registrados:</div><div class="estadValor"><?= $usuarios_registrados ?></div>
					<div class="estadLabel">En l&iacute;nea:</div><div class="estadValor"><?= $usuarios_online ?></div>
					<div class="estadLabel">Apuestas:</div><div class="estadValor"><?= $total_apuestas ?></div>
				</div>
			</div>
			
			<div id="cajaNews" class="bordesCaja">
				<div class="gradiente-verde-negro">Noticias</div>
				<div>test</div>
			</div>

		</div>
		
		<div class="izquierda">
			<div id="medioCont" class="bordesCaja">
				<div class="gradiente-verde-negro">Partidos de la semana</div>
				
				<!-- Partidos de la semana -->
				<div>
					<?php
						if(count($partidos_next_week) > 0) {
					?>
						
							<?php
							foreach($partidos_next_week as $objPartido) {
							?>
								<div class="lineaPartidoSemana">
									
									<div class="ladoVs izquierda"><?= $objPartido->nom_equipo_local; ?></div>
									<div class="centrarVs">v/s</div>
									<div class="ladoVs derecha"><?= $objPartido->nom_equipo_visita; ?></div>
									
									<div class="clear abajoVs"><?= $objPartido->lugar; ?></div>
								</div>
							<?php
							}
							?>

					<?php
						} else {
					?>
						<div>No hay partidos definidos para esta semana</div>
					<?php
						}
					?>
				</div>
			</div>
		</div>
		
		<div class="izquierda">
			<div id="cajaFb" class="bordesCaja">
				<div class="gradiente-verde-negro">Futbolitis en Facebook</div>
				
			</div>
		</div>
		
		<!-- <div class="clear"></div>	div invisible para mantener el contenido dentro del contenedor -->
	</div>

		
	
	
	
