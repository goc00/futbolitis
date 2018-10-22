<?php
	class Constante_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
		// Obtiene todas las constantes
        function getConstantes() {
            $resultado = array();

            $sql = "SELECT c.nombre, c.valor FROM constante c";
			$consulta = $this->db->query($sql);

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
        }
	}
?>