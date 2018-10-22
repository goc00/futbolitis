<table id="maqueta_principal" name="maqueta_principal">
    <tr>
        <td id="seccion_left" name="seccion_left">
			<div id="es_destacado">
				<?php if (!is_null($objPrimerPartido)) echo $objPrimerPartido->titulo; ?>
			</div>
            <div id="estado_partido">
				<script>					
					// Llama al cronómetro que muestra el tiempo restante en formato de fecha válido
					<?php if (!is_null($objPrimerPartido)) { ?>
							cronometro2('<?= date('Y-m-d H:i:s'); ?>', '<?= $objPrimerPartido->fecha_programada; ?>', '<?= $objPrimerPartido->hora_programada; ?>');
					<?php } ?>
				</script>
			</div>

            <?= form_open('principal/apostar', array("id" => "form_apostar")); ?>
            <table align="center" border="1" width="550">
                <?php
                    if (!is_null($objPrimerPartido)) {
						// Almacena el ID del partido para saber sobre que partido se está apostando
                ?>
                        <tr>
                            <td><div id="nombre_partido_local"><?= $objPrimerPartido->nom_equipo_local; ?></div>(local)</td>
                            <td>
                                <div id="partido_lugar"><?= $objPrimerPartido->lugar; ?></div>
                                <div id="partido_fecha_programada"><?= date("d-m-Y", strtotime($objPrimerPartido->fecha_programada)); ?></div>
                                <div id="partido_hora_programada"><?= date("H:i", strtotime($objPrimerPartido->hora_programada)); ?></div>
                            </td>
                            <td><div id="nombre_partido_visita"><?= $objPrimerPartido->nom_equipo_visita; ?></div>(visita)</td>
                        </tr>
                <?php
						// opciones disponibles para apostar
                        foreach($opciones_apostar as $opcion) {
							$vLocal = '';
							$vVisita = '';
							$x = $objPrimerPartido->id_partido;
							$y = $opcion->id_opcion;
							
							$objApuestaExistente = isset($apuestas_existentes[$x][$y]) ? $apuestas_existentes[$x][$y] : NULL;
							
							if($objApuestaExistente) {
								$vLocal = $objApuestaExistente['resultado_local'];
								$vVisita = $objApuestaExistente['resultado_visita'];
							}
                ?>
                            <tr>
                                <td>
                                    <?php
                                        $data = array("id" => "opcion-A-".$opcion->id_opcion, "name" => "opcion-A-".$opcion->id_opcion, "value" => $vLocal);
                                        echo form_input($data);
                                    ?>
                                </td>
                                <td>
                                    <?php
										$opcionOutput = $opcion->nombre;
										
										// Si la opción es plus, muestra tu costo
										if ($opcion->plus) 
											$opcionOutput .= "<br />Costo: <b>".$opcion->costo."</b> balones";
										
										echo $opcionOutput;
									?>
                                </td>
                                <td>
                                    <?php
                                        $data = array("id" => "opcion-B-".$opcion->id_opcion, "name" => "opcion-B-".$opcion->id_opcion, "value" => $vVisita);
                                        echo form_input($data);
                                    ?>
                                </td>
                            </tr>
                <?php
                        } // fin foreach		
                ?>
                        <tr id="boton_apostar">
                            <td colspan="3">
								<?= form_button(array("id" => "apostar_btn", "name" => "apostar_btn", "content" => "Apostar", "type" => "submit")); ?>
							</td>
                        </tr>
                <?php
                    } else {
                ?>
                <tr>
                    <td>No hay partido alguno para esta semana</td>
                </tr>
                <?php
                    }
                ?>
            </table>
            <?= form_close(); ?>
            <table align="center" width="550" id="listado_partidos_disponibles" name="listado_partidos_disponibles" border="0" cellpadding="0" cellspacing="2">
                <tr>
                    <td colspan="4">Lista de partidos para esta semana</td>
                </tr>
                <tr>
                    <td width="40%">Local</td>
                    <td width="5%"> - </td>
                    <td width="40%">Visita</td>
					<td width="15%">Fecha/Hora</td>
                </tr>
                <?php
                    if(count($lista_partidos) <= 1) {
                ?>
                        <tr>
                            <td colspan="4">No hay más partidos para esta semana, vuelve en unos instantes más mientras se cargan.</td>
                        </tr>
                <?php
                    } else {
						// Lista todos los partidos
                        foreach($lista_partidos as $partido) {
							// No muestra el partido que está visualizándose
							if($partido->id_partido != $objPrimerPartido->id_partido) {
                ?>
                            <tr id="partido_<?= $partido->id_partido; ?>" 
									onMouseOver='destacarPartido(this, true)'
									onMouseOut='destacarPartido(this, false)'
									onClick='cargarPartido3(<?= $partido->id_partido; ?>)'
									height="25" bgColor="#FFFFFF">
                                <td><?= $partido->nom_equipo_local; ?></td>
                                <td>v/s</td>
                                <td><?= $partido->nom_equipo_visita; ?></td>
								<td>
									<div><?= $partido->fechaFormat; ?></div>
									<div><?= $partido->horaFormat; ?></div>
								</td>
                            </tr>
                <?php
							}
                        }
                    }
                ?>
            </table>

        </td>
        <td id="seccion_right" name="seccion_right" align="center" width="340" valign="top">
			&Uacute;ltimos mensajes
			<div id="panel_comentarios">
				<!-- Despliega comentarios -->
				<?php
				if(count($comentarios) > 0) {
				?>
				<div id="panel_comentarios_inside">
					<ul>
					<?php
						foreach($comentarios as $objComentario) {
					?>
						<li>
							<span class="com_nombre_usuario"><?= $objComentario->nombre_usuario ?></span>:&nbsp;
							<span class="com_mensaje"><?= $objComentario->mensaje ?></span><br /><span class="com_fecha"><?= $objComentario->fecha ?></span>
						</li>
					<?php
						}
					?>
					</ul>
				</div>
				<?php
				} else {
				?>
					<div class="no_comentario">No hay comentarios para este partido. ¡Deja el tuyo y se el primero!</div>
				<?php
				}
				?>
			</div>
			<div>
				<input type="text" id="caja_mensaje" name="caja_mensaje" />
			</div>
			<div id="chars_allowed"></div>
			<div>
				<?php
					if(!is_null($objPrimerPartido)) {
				?>
						<button id="btn_escribir_mensaje" onClick="javascript: escribirMensaje();">Escribir mensaje</button>
				<?php
					}
				?>
			</div>
		</td>
    </tr>
</table>
<?php
	if(!is_null($objPrimerPartido)) {
?>
		<input type="hidden" id="numero_partido" name="numero_partido" value="<?= $objPrimerPartido->id_partido; ?>" />
<?php
	}
?>