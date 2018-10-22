<?php
	$numBolsas = count($bolsas);
	
	if ($numBolsas > 0) {
?>
		<table align="center" width="510" border="0">
			<tr>
			<?php
				$i = 1;
				foreach($bolsas as $objBolsa) {
			?>
				<td>
					<table align="center">
						<tr>
							<td align="center">Opción <?= $i++; ?></td>
						</tr>
						<tr>
							<td align="center" height="50">Número de créditos: <?= $objBolsa->cantidad; ?></td>
						</tr>
						<tr>
							<td align="center"><?= $objBolsa->precio; ?></td>
						</tr>
						<tr>
							<td align="center">
								<input type="button" value="Comprar" onClick="javascript: comprar('<?= $objBolsa->hash; ?>');" /></a>
							</td>
						</tr>
					</table>
				</td>
			<?php
				}
			?>
			</tr>
		</table>
<?php
	} else {
?>
		No hay bolsas definidas por el momento
<?php
	}
?>
