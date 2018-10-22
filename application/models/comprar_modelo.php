<?php
	class Comprar_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}

		function getBolsas() {
			$resultado = array();

			$consulta = $this->db->get("bolsa");

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
		}
		
		// Compra bolsa
		function comprar($obj) {			
			$info = array(
							"id_usuario" => $obj->id_usuario,
							"id_bolsa" => $obj->id_bolsa,
							"valor" => $obj->valor,
							"fecha" => date('Y-m-d H:i:s')
                        );
            $resultado = $this->db->insert("compra", $info);
			
			return $resultado;
		}
	}
?>