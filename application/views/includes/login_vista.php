<div id="login_div" name="login_div">

    <?php
        $nombre_usuario = "";
        $total_creditos = "";
        $ultimo_acceso = "";
        $mostrar = "";

        if($this->session->userdata("id_usuario")) {
            $mostrar = 'style="display:inline;"';
            $nombre_usuario = $this->session->userdata("nombre_usuario");
            $total_creditos = $this->session->userdata("total_creditos");
            $ultimo_acceso = $this->session->userdata("ultimo_acceso");

            if (!empty($ultimo_acceso)) {
                $arrFecha = explode(" ", $ultimo_acceso);
                list($ano, $mes, $dia) = explode("-", $arrFecha[0]);
                $ultimo_acceso = " | Última visita: " .$dia."-".$mes."-".$ano.", a las ".$arrFecha[1];
            } else {
                $ultimo_acceso = "";
            }
        } else {
    ?>

    <div id="registro_link_div" name="registro_link_div">
        <a class="registrar" href="<?= base_url(); ?>registro">¡Reg&iacute;strate!</a>
    </div>
    <div id="olvida_pass_div" name="olvida_pass_div">
        <a class="olvidar_pass" href="<?= base_url(); ?>usuario/recuperar_contrasena">¿Olvidaste tu contrase&ntilde;a?</a>
    </div>

    <!-- inicio formulario login -->
    <div id="form_login_div" name="form_login_div">
        <?= form_open(base_url().'usuario/login', array('id' => 'formulario_login')); ?>

            <?php
                $info = array("id" => "nom_usuario_txt", "name" => "nom_usuario_txt", "value" => "Usuario", "class" => "input_form_login");
                echo '<div class="float_left_login margen_5px_der">'.form_input($info).'</div>';

                $info = array("id" => "contrasena_mask_txt", "name" => "contrasena_mask_txt", "value" => "Contraseña", "autocomplete" => "off", "class" => "input_form_pass");
                echo '<div class="float_left_login">'.form_input($info).'</div>';

                $info = array("id" => "contrasena_txt", "name" => "contrasena_txt", "autocomplete" => "off", "class" => "input_form_pass");
                echo '<div class="float_left_login">'.form_password($info).'</div>';
            ?>

            <?php
                $info = array("id" => "enviar_btn", "name" => "enviar_btn", "content" => "enviar", "type" => "submit", "class" => "boton_login");
                echo '<div class="float_left_login margen_5px_izq">'.form_button($info).'</div>';
            ?>

        <?= form_close(); ?>
    </div>

    <?php
    }
    ?>

    <div id="bienvenida_p" name="bienvenida_p" <?= $mostrar; ?>>
        ¡Bienvenido <span class="nombre_usuario"><?= $nombre_usuario; ?></span>!
        | <span id="total_creditos_id" name="total_creditos_id"><?= $total_creditos; ?></span> créditos
        <span><?= $ultimo_acceso; ?></span>
        | <a href="<?= base_url(); ?>usuario/logout" class="cerrar_sesion">Cerrar sesión</a>
    </div>
    <div id="login_error_div" name="login_error_div"></div>

</div>