<?php
	class Resultado_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
		// Obtiene todos los resultados de un partido
        function getResultados($idPartido) {
            $resultado = array();

            $sql = "SELECT r.id_opcion, r.resultado_local, r.resultado_visita
					FROM resultado r
					WHERE r.id_partido = ?";
			$consulta = $this->db->query($sql, array($idPartido));

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
        }
	}
?>