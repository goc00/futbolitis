<?php
	class Noticia_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
		
        function getNoticias() {
            $resultado = array();
			
            $consulta = $this->db->get_where("noticia", array("destacado" => 0));

            if ($consulta->num_rows() > 0)
                $resultado = $consulta->result();

            return $resultado;
        }
		
		function noticiaDestacada() {
			$resultado = NULL;
			
			// Obtiene la noticia más actual y destacada
			$sql = "SELECT id_noticia, titulo, contenido, ruta_imagen
					FROM noticia
					WHERE fecha = (
									SELECT MAX(fecha)
									FROM noticia
									WHERE destacado = 1
								)";
			
			$consulta = $this->db->query($sql);
			if ($consulta->num_rows() > 0) $resultado = $consulta->row();
			
			return $resultado;
		}
		
		function masInfo($idNoticia) {
			$resultado = NULL;
			
            $consulta = $this->db->get_where("noticia", array("id_noticia" => $idNoticia));

            if ($consulta->num_rows() > 0) {
                $resultado = $consulta->row();
				
				// Actualiza el número de vistas para la noticia
				$nuevoNum = (int)$resultado->veces_leida+1;
				$info = array("veces_leida" => $nuevoNum);
				$resultado-> veces_leida = $nuevoNum;
				
				$this->db->where("id_noticia", $idNoticia);
				$this->db->update("noticia", $info);
			}

            return $resultado;
		}
	}
?>