<?= form_open("registro/registrar", array("id" => "registro_form")); ?>
 
	<table align="center" cellpadding="2" cellspacing="0" border="0" width="80%">
		<tr>
			<td align="left">Nombre de usuario</td>
			<td align="left"><input type="text" id="reg_nombre_usuario_txt" name="reg_nombre_usuario_txt" class="reg_input" /></td>
		</tr>
		<tr>
			<td align="left">Contrase&ntilde;a</td>
			<td align="left"><input type="password" id="reg_contrasena_txt" name="reg_contrasena_txt" class="reg_input" /></td>
		</tr>
		<tr>
			<td align="left">Repite tu contrase&ntilde;a</td>
			<td align="left"><input type="password" id="reg_re_contrasena_txt" name="reg_re_contrasena_txt" class="reg_input" /></td>
		</tr>
		<tr>
			<td align="left">E-mail</td>
			<td align="left"><input type="text" id="reg_email_txt" name="reg_email_txt" class="reg_input" /></td>
		</tr>
		<tr>
			<td align="left">Repite tu e-mail</td>
			<td align="left"><input type="text" id="reg_re_email_txt" name="reg_re_email_txt" class="reg_input" /></td>
		</tr>
		<tr>
			<td colspan="2"><div style="background-color: #000000; height: 10px;">&nbsp;</div></td>
		</tr>
		<tr>
			<td align="left">Nombre</td>
			<td align="left"><input type="text" id="reg_nombre_txt" name="reg_nombre_txt" class="reg_input" /></td>
		</tr>
		<tr>
			<td align="left">Apellido paterno</td>
			<td align="left"><input type="text" id="reg_apellido_pat_txt" name="reg_apellido_pat_txt" class="reg_input" /></td>
		</tr>
		<tr>
			<td align="left">Apellido materno</td>
			<td align="left"><input type="text" id="reg_apellido_mat_txt" name="reg_apellido_mat_txt" class="reg_input" /></td>
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
					foreach($regiones as $objRegion) {
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
    
<?= form_close(); ?>