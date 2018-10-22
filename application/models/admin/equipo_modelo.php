<?php
	class Equipo_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}

        function traer() {
            $this->db->select('a.id_equipo as id_equipo, a.nombre as nom_equipo, b.nombre as nom_pais');
            $this->db->from('equipo a');
            $this->db->join('pais b', 'a.id_pais = b.id_pais');

            return $this->db->get();
        }

        function agregar($obj) {
            $resultado = true;

            $info = array("id_pais" => $obj->id_pais,
                            "nombre" => $obj->nombre);
            $this->db->insert("equipo", $info);

            // Si se generó algún error en la inserción
            if ($this->db->affected_rows() == 0) $resultado = false;

            return $resultado;
        }

	}
?>