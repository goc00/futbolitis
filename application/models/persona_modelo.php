<?php
	class Persona_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}

        function getPersona($idPersona) {
            $resultado = NULL;

            $consulta = $this->db->get_where("persona", array("id_persona" => $idPersona), 1, 0);
            if ($consulta->num_rows() > 0) $resultado = $consulta->row();

            return $resultado;
        }
		
		// Recibe un stdClass (como objeto Usuario) para actualizarlo
		function actualiza($objPersona) {
            $resultado = FALSE;

            $infoPersona = array(
                                    "nombre" => strtoupper($objPersona->nombre),
                                    "apellido_paterno" => strtoupper($objPersona->apellido_paterno),
                                    "apellido_materno" => strtoupper($objPersona->apellido_materno),
                                    "direccion" => strtoupper($objPersona->direccion),
									"pais" => $objPersona->pais,
									"region" => $objPersona->region,
									"provincia" => $objPersona->provincia,
									"comuna" => $objPersona->comuna,
                                    "telefono_particular" => $objPersona->telefono_particular,
                                    "telefono_celular" => $objPersona->telefono_celular
                                );
								
            $this->db->where("id_persona", $objPersona->id_persona);
            $this->db->update("persona", $infoPersona);

            // Si se generó algún error en la inserción
            if ($this->db->affected_rows() > 0) $resultado = TRUE;

            return $resultado;
        }
	}
?>