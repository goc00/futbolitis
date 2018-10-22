<?php
	class Comentario_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
		// Obtiene todos los comentarios
        function getComentarios($idPartido, $limit) {
            $resultado = array();
			
			// Solo trae los comentarios APROBADOS
            $sql = "SELECT c.id_comentario, u.nombre_usuario, c.mensaje, c.fecha
					FROM comentario c, usuario u
					WHERE c.id_partido = ?
					  AND c.id_usuario = u.id_usuario
					  AND c.aprobado = 1
					ORDER BY c.fecha DESC
					LIMIT 0, ?";
			$consulta = $this->db->query($sql, array((int)$idPartido, (int)$limit));

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
        }
		
		// Inserta un comentario y devuelve la info del comentario
		function insertar($idPartido, $idUsuario, $mensaje) {
			$info = array(
							"id_partido" => $idPartido,
							"id_usuario" => $idUsuario,
							"mensaje" => $mensaje,
							"fecha" => date('Y-m-d H:i:s'),
							"aprobado" => 0
                        );
			return $this->db->insert("comentario", $info);
		}
		
		// Trae el comentario con respecto a ID de partido e ID de usuario
		function getFechaUltimoComentario($idPartido, $idUsuario) {
			$resultado = NULL;
			
			$sql = "SELECT MAX(c.fecha) as fecha
					FROM comentario c
					WHERE c.id_partido = ? AND c.id_usuario = ?";
			$consulta = $this->db->query($sql, array($idPartido, $idUsuario));
			$numRows = $consulta->num_rows();

            if ($numRows > 0) {
                $resultado = $consulta->row();
			} elseif($numRows == 0) {
				$resultado = new stdClass();
			}

            return $resultado;
		}
	}
?>