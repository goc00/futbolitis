<form id="pre_form" name="pre_form" method="post" action="<?= base_url() ?>preinscripcion/completar">
	<table align="center" border="1" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left">Nombre</td>
			<td align="left"><input type="text" id="nombre_txt" name="nombre_txt" /></td>
		</tr>
		<tr>
			<td align="left">Apellidos</td>
			<td align="left">
				<input type="text" id="ape_pat_txt" name="ape_pat_txt" />
				<input type="text" id="ape_mat_txt" name="ape_mat_txt" />
			</td>
		</tr>
		<tr>
			<td align="left">Pa&iacute;s</td>
			<td align="left">
				<select id="pais_cmb" name="pais_cmb" disabled="disabled">
					<option value="2">Chile</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left">Regi&oacute;n</td>
			<td align="left">
				<select id="region_cmb" name="region_cmb">
					<option value="">Selecciona tu opción</option>
					<?php
					foreach($info["regiones"] as $objRegion) {
					?>
					<option value="<?= $objRegion->REGION_ID ?>"><?= $objRegion->REGION_NOMBRE ?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left">Provincia</td>
			<td align="left">
				<select id="provincia_cmb" name="provincia_cmb">
					<option value="">Selecciona tu opci&oacute;n</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left">Comuna</td>
			<td align="left">
				<select id="comuna_cmb" name="comuna_cmb">
					<option value="">Selecciona tu opci&oacute;n</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="left">Direcci&oacute;n</td>
			<td align="left">
				<input type="text" id="dire_txt" name="dire_txt" />
				<select id="tipo_dire_txt">
					<option value="casa">Casa</option>
					<option value="depto">Departamento</option>
				</select>
				<input type="text" id="num_dire_txt" name="num_dire_txt" size="3" />
			</td>
		</tr>
		<tr>
			<td align="left">Tel&eacute;fono particular</td>
			<td align="left">
				<input type="text" id="cod_tel_txt" size="3" disabled="disabled" value="+56" /> - 
				<input type="text" id="dig_tel_txt" size="3" /> - 
				<input type="text" id="tel_txt" name="tel_txt" />
			</td>
		</tr>
		<tr>
			<td align="left">Celular (opcional)</td>
			<td align="left">
				<input type="text" id="cod_cel_txt" size="3" disabled="disabled" value="+569" /> -
				<select id="dig_cel_txt">
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
				</select> -
				<input type="text" id="cel_txt" name="cel_txt" />
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<input type="submit" id="enviar_btn" name="enviar_btn" />
			</td>
		</tr>
	</table>
	<input id="hash" name="hash" type="hidden" value="<?= $info["id_usuario"]; ?>" />
</form>
<div id="error_pre_salida"></div>