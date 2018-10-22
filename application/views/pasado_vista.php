

<div id="accordion">
	<?php
		$grpDestacados = $partidos_agrupados['destacados'];
		$grpPorFecha = $partidos_agrupados['por_fecha'];
	?>
	
	<h3><a href="#">Destacados</a></h3>
	<?php
	// Partidos DESTACADOS
	if (count($grpDestacados) > 0) {
		// Crea un accordion con los partidos agrupados
	?>
		<div>
	<?php
		foreach($grpDestacados as $objPartidoDestacado) {
	?>
			<p id="part_destacado_<?= $objPartidoDestacado->id_partido; ?>" class="sobre_div" onClick="verResultados(this)">
				<?= $objPartidoDestacado->nom_equipo_local; ?> v/s <?= $objPartidoDestacado->nom_equipo_visita; ?>
			</p>
	<?php
		}
	?>
		</div>
	
<?php 
	} else {
?>
	<div><p>No existen partidos <b>destacados</b> por revisar</p></div>
<?php 
	}
	
	// Partidos agrupados POR FECHA
	if(count($grpPorFecha) > 0) {
		
		$dias = array('lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo');
		$meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		
		foreach($grpPorFecha as $clave => $valor) {
			$dia = $dias[(int)date('N', strtotime($clave)) - 1];
			$mes = $meses[(int)date('n', strtotime($clave)) - 1];
			$fechaFormated = $dia . ' ' . date('d', strtotime($clave)) . ' de ' . $mes . ', ' . date('Y', strtotime($clave));
			echo '<h3><a href="#">'.$fechaFormated.'</a></h3>';
			echo '<div>';
			
			// Dentro de cada grupo se encuentran los partidos
			$arrPartidos = $valor;
			foreach($arrPartidos as $objPartido) {
				echo '<p id="part_fecha_'.$objPartido->id_partido.'" class="sobre_div" onClick="verResultados(this)">'.$objPartido->nom_equipo_local.' v/s '.$objPartido->nom_equipo_visita.'</p>';
			}
			echo '</div>';
		}
?>
<?php
	} else {
?>
	No existen partidos anteriores por revisar
<?php
	}
?>

</div>
