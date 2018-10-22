<div>País</div>

<div>
    <?= form_open("admin/pais/mantenedor/add", array("id" => "form_pais", "name" => "form_pais")) ?>
    <table align="left" border="0" cellpadding="5">
        <tr>
            <td>
            Nombre: <?= form_input(array("id" => "nombre_pais_txt", "name" => "nombre_pais_txt")); ?>
            </td>
        </tr>
        <tr>
            <td>
            Sigla: <?= form_input(array("id" => "sigla_txt", "name" => "sigla_txt")); ?>
            &nbsp;<?= form_submit("submitPais", "agregar"); ?>
            </td>
        </tr>
        <tr>
            <td>
                <table align="left" border="1">
                    <tr>
                        <td>Nombre</td>
                        <td>Sigla</td>
                        <td>Acciones</td>
                    </tr>
                <?php
                // contenidoExtra trae todos los registros de la tabla
                if($contenidoExtra->num_rows() == 0) {
                ?>
                    <tr>
                        <td colspan="3">No hay registros</td>
                    </tr>
                <?php
                } else {
                    foreach($contenidoExtra->result() as $fila) {
                ?>
                    <tr>
                        <td><?= $fila->nombre; ?></td>
                        <td><?= $fila->sigla; ?></td>
                        <td><a href="pais/mantenedor/delete/<?= $fila->id_pais; ?>">Eliminar</a></td>
                    </tr>
                <?php
                    }
                }
                ?>
                </table>
            </td>
        </tr>
    </table>
</div>