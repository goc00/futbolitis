<?php
	class Partido_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
		// Obtiene los partidos de una semana en particular
        function getPartidos($diaInicio, $diaFinal) {
            $resultado = array();

            $sql = "SELECT p.id_partido
					FROM partido p
					WHERE DATE(p.fecha_programada) BETWEEN ? AND ?";
			$consulta = $this->db->query($sql, array($diaInicio,$diaFinal));

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
        }
		
		// Obtiene el partido por su ID
		function getPartido($idPartido) {
            $resultado = NULL;

            $sql = "SELECT p.*
					FROM partido p
					WHERE p.id_partido = ?";
			$consulta = $this->db->query($sql, $idPartido);

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->row();

            return $resultado;
        }
		
		// Puede recibir el nombre del campo o un arreglo de estos, para generar dinámicamente la consulta
		function getValorCampo($idPartido, $campos) {
			$sql = "";
			$resultado = NULL;
			
			// Verifica el tipo del parámetro
			if(is_string($campos)) {
				$sql = "SELECT p.$campos
					    FROM partido p
						WHERE p.id_partido = ?";
			} elseif(is_array($campos)) {
				// Genera el SELECT
				$select = "";
				for($i=0; $i<count($campos); $i++) $select .= "p.".$campos[$i].",";
				// Quita último caracter
				$select = substr($select, 0, strlen($select)-1);
				
				$sql = "SELECT $select
						FROM partido p
						WHERE p.id_partido = ?";
			}
			
			$consulta = $this->db->query($sql, $idPartido);

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->row();

            return $resultado;
		}
	}
?>