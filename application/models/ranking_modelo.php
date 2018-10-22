<?php
	class Ranking_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
        function getPosiciones($desde, $numero) {
            $resultado = array();
			
			$sql = "SELECT u.id_usuario, u.nombre_usuario, SUM(IF(r.puntaje IS NULL, 0, r.puntaje)) as puntaje
					FROM usuario u LEFT JOIN ranking r ON u.id_usuario = r.id_usuario
					GROUP BY id_usuario, nombre_usuario
					ORDER BY puntaje DESC
					LIMIT ?,?";
			$consulta = $this->db->query($sql, array($desde, $numero));
			
			if ($consulta->num_rows() > 0) {
				$resultado = $consulta->result();
			}
			
			return $resultado;		
		}

	}
?>