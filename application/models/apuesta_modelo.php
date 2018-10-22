<?php
	class Apuesta_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
		// Va generando las apuestas del usuario
        function insertar($idPartido, $idUsuario, $apuestas, $totalCreditos) {
            $resultado = true;
			
			// Inicia la transacción
			$this->db->trans_start();
			
			foreach ($apuestas as $obj) {
				// Verifica si la apuesta ya fue realizada, por ende:
				// Si la apuesta existe = ACTUALIZA el registro
				// Si la apuesta NO existe = INSERTA el nuevo registro
				$vIdOpcion = $obj["id_opcion"];
				$vLocal = $obj["local"];
				$vVisita = $obj["visita"];
				
				if($this->apuestaExiste($idPartido, $idUsuario, $vIdOpcion)) {
					$info = array(
								"resultado_local" => $vLocal,
								"resultado_visita" => $vVisita,
								"fecha" => date("Y-m-d H:i:s")
							);
					$this->db->where(array("id_partido" => $idPartido, "id_usuario" => $idUsuario, "id_opcion" => $vIdOpcion));
					$this->db->update("apuesta", $info);
				} else {
					$info = array(
								"id_partido" => $idPartido,
								"id_usuario" => $idUsuario,
								"id_opcion" => $vIdOpcion,
								"resultado_local" => $vLocal,
								"resultado_visita" => $vVisita,
								"fecha" => date("Y-m-d H:i:s")	// fecha y hora actual
							);
					$this->db->insert("apuesta", $info);
				}
			}
			
			// Actualiza el total de créditos
			if($totalCreditos > 0) {
				$sql = "UPDATE usuario"
						." SET total_creditos = total_creditos - " . $totalCreditos
						." WHERE id_usuario = " . $idUsuario;
				$consulta = $this->db->query($sql);
			}
			
			// Fin transacción
			$this->db->trans_complete();
			
			// Si existió algún error en la transacción
			if ($this->db->trans_status() === FALSE) $resultado = false;
			

            return $resultado;
        }
		
		// Verifica si la apuesta existe o no
		function apuestaExiste($idPartido, $idUsuario, $idOpcion) {
			$resultado = FALSE;

            $consulta = $this->db->get_where("apuesta",
                                             array("id_partido" => $idPartido,
                                                   "id_usuario" => $idUsuario,
												   "id_opcion" => $idOpcion), 1, 0);

            if ($consulta->num_rows() > 0) $resultado = TRUE;

            return $resultado;
		}
		
		// Busca una apuesta en particular, con el id_partido, id_usuario e id_opcion
		function buscaApuesta($idPartido, $idUsuario, $idOpcion) {
			$resultado = NULL;

            $consulta = $this->db->get_where("apuesta",
                                             array("id_partido" => $idPartido,
                                                   "id_usuario" => $idUsuario,
												   "id_opcion" => $idOpcion), 1, 0);

            if ($consulta->num_rows() > 0) $resultado = $consulta->row();

            return $resultado;
		}
		// Obtiene apuestas de un usuario sobre un partido en particular
		function getApuestasUP($idUsuario, $idPartido) {
			$resultado = array();
			
			$sql = "SELECT a.id_apuesta, a.id_opcion, a.id_partido, a.resultado_local, a.resultado_visita
					FROM apuesta a
					WHERE a.id_usuario = ? AND a.id_partido = ?";
			$consulta = $this->db->query($sql, array($idUsuario, $idPartido));
			
			if ($consulta->num_rows() > 0) $resultado = $consulta->result();
			
			return $resultado;
		}
		
		function totalApuestas() {
			return $this->db->count_all("apuesta");
		}

	}
?>