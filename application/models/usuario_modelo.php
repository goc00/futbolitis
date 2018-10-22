<?php
	class Usuario_modelo extends CI_Model {
	
		function __construct() {
			// Llama al constructor padre
			parent::__construct();
		}
	
		// Trae al usuario por id
		function getUsuario($idUsuario) {
			$resultado = NULL;

            $consulta = $this->db->get_where("usuario", array("id_usuario" => $idUsuario), 1, 0);

            if ($consulta->num_rows() > 0) $resultado = $consulta->row();

            return $resultado;
		}
	
        /* Verifica que el usuario y password son correctos.
         * El nombre de la tabla por defecto es "usuario" */
        function compruebaUsuario($nombre_usuario, $pass, $tabla="usuario") {
            // Resultado del método
            $resultado = NULL;

            // $query = $this->db->get_where('mytable', array('id' => $id), $limit, $offset);
            $consulta = $this->db->get_where($tabla,
                                                array("nombre_usuario" => $nombre_usuario,
                                                      "contrasena" => $pass), 1, 0);

            // Si encuentra un usuario
            // Se utiliza row() porque devuelve 1 solo registro, row() retorna un OBJETO
            if ($consulta->num_rows() > 0)
                $resultado = $consulta->row();

            return $resultado;
        }


        /* Recibirá un objeto con toda la información para la creación del usuario.
         * Se utilizarán transacciones para mantener la integridad de los datos,
         * hará rollback o commit automáticamente dependiendo si se generó o no
         * un error al insertar en las tablas correspondientes */
        function creaUsuario($objUsuario) {
            $resultado = true;

            $this->db->trans_start();

            $infoPersona = array(
                                    "nombre" => strtoupper($objUsuario->nombre),
                                    "apellido_paterno" => strtoupper($objUsuario->apellido_paterno),
                                    "apellido_materno" => strtoupper($objUsuario->apellido_materno),
                                    "direccion" => strtoupper($objUsuario->direccion),
									"pais" => $objUsuario->pais,
									"region" => $objUsuario->region,
									"provincia" => $objUsuario->provincia,
									"comuna" => $objUsuario->comuna,
                                    "telefono_particular" => $objUsuario->telefono_particular,
                                    "telefono_celular" => $objUsuario->telefono_celular
                                );
            $this->db->insert("persona", $infoPersona);

            $infoUsuario = array(
                                    "id_persona" => $this->db->insert_id(),
                                    "id_estado" => 1,
                                    "nombre_usuario" => $objUsuario->nombre_usuario,
                                    "contrasena" => md5($objUsuario->contrasena),
                                    "email" => $objUsuario->email,
									"fecha_creacion" => date('Y-m-d H:i:s'),
                                    "total_creditos" => 0
                                );
            $this->db->insert("usuario", $infoUsuario);

            $this->db->trans_complete();

            // Si se generó algún error en la inserción
            if ($this->db->trans_status() === FALSE) $resultado = false;

            return $resultado;
        }
		
		// Trae usuarios activos, esto significa que se encuentren en estado NORMAL
		// y que además hayan pasado al menos N meses desde su último acceso
		function usuariosActivos($meses) {
			$resultado = array();
			
			$sql = "SELECT t.id_usuario, t.nombre_usuario
					FROM usuario t
					WHERE t.ultimo_acceso BETWEEN DATE_ADD(NOW(), INTERVAL -? MONTH) AND NOW()";
					
			$consulta = $this->db->query($sql, $meses);
			if ($consulta->num_rows() > 0) $resultado = $consulta->result();
					
			return $resultado;
		}
		
		
		// Actualiza último acceso
		function actualizaAcceso($idUsuario) {
			$info = array("ultimo_acceso" => date("Y-m-d H:i:s"));

            $this->db->where("id_usuario", $idUsuario);
            $this->db->update("usuario", $info);
		}
		
        /* Verifica si ya existe el usuario y e-mail en la base de datos */
        function existeNombreUsuario($nombre_usuario, $tabla="usuario") {
            $resultado = false;

            $consulta = $this->db->get_where($tabla, array("nombre_usuario" => $nombre_usuario), 1, 0);
            if ($consulta->num_rows() > 0) $resultado = true;

            return $resultado;
        }

         function existeEmail($email, $tabla="usuario") {
            $resultado = false;

            $consulta = $this->db->get_where($tabla, array("email" => $email), 1, 0);
            if ($consulta->num_rows() > 0) $resultado = true;

            return $resultado;
        }
		
		
		// RECUPERAR PASSWORD
		function recuperarPass($obj) {
			$resultado = "no";
			
			if(!is_null($this->compruebaUsuario($obj->nombre_usuario, $obj->pass))) {
				// Actualiza el password

				$info = array("contrasena" => $obj->new_pass);
									
				$this->db->where("nombre_usuario", $obj->nombre_usuario);
				$this->db->update("usuario", $info);

				// Si se generó algún error en la actualización
				if ($this->db->affected_rows() > 0) $resultado = "si";

			} else {
				$resultado = "no_user";
			}
			
			return $resultado;
		}
		
		// Borra las IP que llevan más de 30 minutos en el sistema
		function borrarControlIP($limite) {
			$sql = "DELETE FROM control_ip WHERE fecha < ?";
			$this->db->query($sql, $limite);
		}
		
		// Revisa si existe la IP del usuario
		function existeIP($ip) {
			$resultado = FALSE;
			
            $consulta = $this->db->get_where("control_ip", array("ip" => $ip), 1, 0);
            if ($consulta->num_rows() > 0) $resultado = TRUE;

            return $resultado;
		}
		
		function actualizaIP($fecha, $ip) {
			$info = array("fecha" => $fecha);
									
			$this->db->where("ip", $ip);
			$this->db->update("control_ip", $info);
		}
		
		function insertaIP($fecha, $ip) {
			$info = array("ip" => $ip, "fecha" => $fecha);
            $this->db->insert("control_ip", $info);
		}
		
		function totalIP() {
			return $this->db->count_all("control_ip");
		}
		
		function totalUsuarios() {
			return $this->db->count_all("usuario");
		}
	}
?>