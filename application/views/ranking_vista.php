hola mundo!<br />
<?php
	$numJugadores = count($posiciones);
	if ($numJugadores > 0) {
?>
	<table align="center" width="500" border="1">
		<tr>
			<td>Posición</td>
			<td>Jugador</td>
			<td>Puntaje</td>
		</tr>
		<?php
			foreach($posiciones as $obj) {
				// Destaca la fila si aparece el jugador
				$destacar = ($obj->marcar) ? 'destacar_fila_rank' : 'fila_rank';
		?>
				<tr class="<?= $destacar; ?>">
					<td><?= $obj->posicion; ?></td>
					<td><?= $obj->nombre_usuario; ?></td>
					<td><?= $obj->puntaje; ?></td>
				</tr>
		<?php
			}
		?>
	</table>
	Total de jugadores: <?= $numJugadores; ?>
<?php 
	} else {
?>
	No hay registros de puntaje por el momento
<?php 
	}
?>