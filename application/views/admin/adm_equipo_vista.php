<div>Equipo</div>

<div>
    <?= form_open("admin/equipo/mantenedor/add", array("id" => "form_equipo", "name" => "form_equipo")) ?>
    <table align="left" border="0" cellpadding="5">
        <tr>
            <td>País: <?= form_input(array("id" => "nombre_pais_txt", "name" => "nombre_pais_txt")); ?></td>
        </tr>
        <tr>
            <td>Nombre: <?= form_input(array("id" => "nombre_equipo_txt", "name" => "nombre_equipo_txt")); ?></td>
        </tr>
        <tr>
            <td>
                <table align="left" border="1">
                    <tr>
                        <td>Nombre</td>
                        <td>País</td>
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
                        <td><?= $fila->nom_equipo; ?></td>
                        <td><?= $fila->nom_pais; ?></td>
                        <td><a href="equipo/mantenedor/delete/<?= $fila->id_equipo; ?>">Eliminar</a></td>
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