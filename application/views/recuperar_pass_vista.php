<?= form_open("usuario/recuperar_contrasena/go", array("id" => "form_rec_pass")); ?>
 
	<table align="center" cellpadding="2" cellspacing="0" border="0" width="80%">
		<tr>
			<td align="left">Nombre de usuario</td>
			<td align="left"><input type="text" id="nom_re_usuario_txt" name="nom_re_usuario_txt" /></td>
		</tr>
		<tr>
			<td align="left">Contrase&ntilde;a anterior</td>
			<td align="left"><input type="password" id="pass_ant_txt" name="pass_ant_txt" /></td>
		</tr>
		<tr>
			<td align="left">Nueva contrase&ntilde;a</td>
			<td align="left"><input type="password" id="pass_new_txt" name="pass_new_txt" /></td>
		</tr>
		<tr>
			<td align="left">Repetir contrase&ntilde;a</td>
			<td align="left"><input type="password" id="pass_re_new_txt" name="pass_re_new_txt" /></td>
		</tr>
		<tr>
			<td align="center" colspan="2">
				<input type="submit" id="enviar_btn_re_pass" name="enviar_btn_re_pass" />
			</td>
		</tr>
	</table>
    
<?= form_close(); ?>