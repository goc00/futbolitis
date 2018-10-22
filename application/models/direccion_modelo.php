<?php
	class Direccion_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}

        /// Trae todas las regiones
        function getRegiones() {
            $resultado = NULL;

            $consulta = $this->db->get("region");

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
        }

		function paraLlenar($id, $tabla) {
			$resultado = NULL;
			$nomCampo = "";
			
			switch($tabla) {
				case "provincia":
					$nomCampo = "PROVINCIA_REGION_ID";
					break;
				case "comuna":
					$nomCampo = "COMUNA_PROVINCIA_ID";
					break;
			}

            $consulta = $this->db->get_where($tabla, array($nomCampo => $id));

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
		}

	}
?>