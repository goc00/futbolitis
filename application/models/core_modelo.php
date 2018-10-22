<?php
	class Core_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}

        function getListaPartidos($diaInicio, $diaFinal) {
            $resultado = array();

            $sql = "SELECT *
                    FROM partido
                    WHERE DATE(fecha_programada) BETWEEN ? AND ?
					ORDER BY destacado DESC, fecha_programada DESC, hora_programada DESC";

            $consulta = $this->db->query($sql, array($diaInicio, $diaFinal));

            if ($consulta->num_rows() > 0) {

                foreach($consulta->result() as $fila) {
                    $objPartido = new stdClass();
                    $objPartido->id_partido = $fila->id_partido;
                    $objPartido->id_equipo_local = $fila->id_equipo_local;
                    $objPartido->id_equipo_visita = $fila->id_equipo_visita;
					$objPartido->fecha_programada = $fila->fecha_programada;
					$objPartido->hora_programada = $fila->hora_programada;
					$objPartido->lugar = $fila->lugar;
					$objPartido->destacado = $fila->destacado;
					$objPartido->vigente = "0";

                    // Va a buscar el nombre de los equipos
                    $sql = "SELECT nombre FROM equipo WHERE id_equipo = ?";

                    $consulta = $this->db->query($sql, array($objPartido->id_equipo_local));
                    if ($consulta->num_rows() > 0) {
                        $res = $consulta->row();
                        $objPartido->nom_equipo_local = $res->nombre;
                    } else {
                        $objPartido->nom_equipo_local = "Equipo local no definido";
                    }

                    $consulta = $this->db->query($sql, array($objPartido->id_equipo_visita));
                    if ($consulta->num_rows() > 0) {
                        $res = $consulta->row();
                        $objPartido->nom_equipo_visita = $res->nombre;
                    } else {
                        $objPartido->nom_equipo_visita = "Equipo local no definido";
                    }

                    $resultado[] = $objPartido;
                }

            }

            return $resultado;
        }
	}
?>