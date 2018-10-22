<?php
	class Pais_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}

        function traePaises() {
            return $this->db->get("pais");
        }

        function agregar($obj) {
            $resultado = true;

            $info = array("nombre" => $obj->nombre,
                            "sigla" => $obj->sigla);
            $this->db->insert("pais", $info);

            // Si se generó algún error en la inserción
            if ($this->db->affected_rows() == 0) $resultado = false;

            return $resultado;
        }

        function eliminar($obj) {
            $resultado = true;

            $this->db->where("id_pais", $obj->id_pais);
            $this->db->delete("pais");
            if ($this->db->affected_rows() == 0) $resultado = false;

            return $resultado;
        }

        function actualizar($obj) {
            $resultado = true;

            $info = array("nombre" => $obj->nombre,
                            "sigla" => $obj->sigla);
            $this->db->where("id_pais", $obj->id_pais);
            $this->db->update("pais", $info);
            if ($this->db->affected_rows() == 0) $resultado = false;

            return $resultado;
        }

	}
?>