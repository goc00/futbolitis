<?php
	class Opcion_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
        function getOpciones() {
            $resultado = array();
			
            $consulta = $this->db->get("opcion");

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
        }
		
		function getOpcion($idOpcion) {
			$resultado = NULL;
			
			$consulta = $this->db->get_where("opcion", array("id_opcion" => $idOpcion));

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->row();

            return $resultado;
		}

	}
?>