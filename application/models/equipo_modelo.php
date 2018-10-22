<?php
	class Equipo_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
		// Obtiene todas las constantes
        function getEquipo($idEquipo) {
            $resultado = NULL;

			$consulta = $this->db->get_where("equipo", array("id_equipo" => (int)$idEquipo));
            if ($consulta->num_rows() > 0) $resultado = $consulta->row();

            return $resultado;
        }
	}
?>