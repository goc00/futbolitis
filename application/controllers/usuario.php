<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 15-01-2012
 | Nombre: usuario.php
 |
 | Descripción:	Controlador sobre las acciones del usuario
 | 
 | Cambios
 | -------
 |	02-03-2012:
 |	- Agrega verificación de estado inicial al momento
 |	de login, esto para verificar si se está autentificando
 |	de manera normal, por pre-inscripción o es login inicial
 |	(primera vez). Todo esto para desplegar el mensaje que
 |	corresponda (si aplica por supuesto).
 |	
 |	05-03-2012:
 |	- Cambio de lógica en tipo de autentificación. Se agrega
 |	método para completar información del usuario pre-inscrito.
 
 */

class Usuario extends CI_Controller {

	function __construct() {
		parent::__construct();

		// Carga el modelo, conectándolo con la base de datos
		$this->load->model("usuario_modelo", "", TRUE);
		$this->load->model("persona_modelo", "", TRUE);
	}
	
	
	function login() {
		// Esta variable enviará respuesta a JQuery
		$salida = "";

		// Reglas para la validación del formulario de login
		$reglas = array
						(
							array('field' => 'nom_usuario_txt', 'label' => 'Usuario', 'rules' => 'trim|required'),
							array('field' => 'contrasena_txt', 'label' => 'Contraseña', 'rules' => 'trim|required')
						);
		// Mensajes propios para los errores
		$this->form_validation->set_message("required", "- El campo %s es requerido");

		// Seteo de reglas
		$this->form_validation->set_rules($reglas);

		// Si la validación cumple todas las reglas
		if ($this->form_validation->run()) {

			$usuarioPost = $this->input->post("nom_usuario_txt");
			$passPost = md5($this->input->post("contrasena_txt"));

			// Trae un objeto nulo o con la info del usuario
			// Recibe por POST la información {nom1 : val1, nom2 : val2}
			$objetoUsuario = $this->usuario_modelo->compruebaUsuario($usuarioPost, $passPost);

			if (!is_null($objetoUsuario)) {

				/*
					Si se encontró el usuario, verifica su estado:
					1 -> Habilitado / normal
					2 -> Deshabilitado / eliminado
					3 -> Baneado
				*/
				
				$idUsuario = $objetoUsuario->id_usuario;
				
				switch ($objetoUsuario->id_estado) {
					case 1:
					
						// Tipo de autentificación
						// N: normal, P: pre-inscripción, I:inicial
						$tL = "";
						$tiposLogin = array("normal" => "N",
											"pre" => "P",
											"inicial" => "I");
					
						if(is_null($objetoUsuario->ultimo_acceso)) {
							// Si es NULL, puede ser por ingreso de pre-inscripción o inicial
							// Busca los datos dentro de persona
							$datosPersona = $this->persona_modelo->getPersona($objetoUsuario->id_persona);
							
							if($datosPersona->nombre == "---"
								&& $datosPersona->apellido_materno == "---"
								&& $datosPersona->apellido_paterno == "---"
								&& $datosPersona->direccion == "---") {
								
								// Llega por pre-inscripción
								$tL = $tiposLogin["pre"];
							} else {
								// Inicial
								$tL = $tiposLogin["inicial"];
							}
						} else {
							// Si no es NULL, es un ingreso normal
							$tL = $tiposLogin["normal"];
						}
						
						// Si es ingreso normal o inicial, actualiza el ingreso de inmediato
						if($tL == "N" || $tL == "I") {
							// Crea la sesión con la información del usuario
							$nuevaInfo = array (
											"id_usuario" => $idUsuario,
											"id_persona" => $objetoUsuario->id_persona,
											"id_estado" => $objetoUsuario->id_estado,
											"nombre_usuario" => $objetoUsuario->nombre_usuario,
											"email" => $objetoUsuario->email,
											"total_creditos" => $objetoUsuario->total_creditos,
											"ultimo_acceso" => $objetoUsuario->ultimo_acceso
										);

							$this->session->set_userdata($nuevaInfo);
							$this->usuario_modelo->actualizaAcceso($idUsuario);	// Actualiza el acceso del usuario	
						}
						
						// Ingreso normal con tipo de autentificación
						// Como el ID del usuario pasará como parámetro (se verá en la URL), se codifica para elevar seguridad
						$salida = array("exito" => "si",
										"tipo_login" => $tL,
										"id_usuario" =>  $this->encrypt->encode($objetoUsuario->nombre_usuario."+".$idUsuario."+".$objetoUsuario->id_persona));

						break;
					case 2:
						// Usuario baneado
						$salida = array("exito" => "no",
										"mensaje" => "<p>Tu cuenta ha sido baneada por el administrador</p>");
						break;
					default:
						// No action
						$salida = array("exito" => "no",
										"mensaje" => "<p>Error desconocido. Si el error persiste, por favor contáctate con el Administrador.</p>");
						break;
				}

			} else {
				// Usuario NO encontrado
				$salida = array("exito" => "no",
								"mensaje" => "<p>Usuario no encontrado</p>");
			}
		} else {
			// No se han completado los campos
			$error = validation_errors();
			$salida = array("exito" => "no",
							"mensaje" => $error);
		}
		
		// Envía arreglo de respuesta "encodado" en formato json para que lo lea JQuery
		echo json_encode($salida);
	}
	
	// Cerrar sesión
	function logout() {
		$this->session->sess_destroy();
		redirect(index_page());
	}
	
	// Recuperar contraseña
	function recuperar_contrasena($procesar = NULL) {
		if(!is_null($procesar)) {
			// Proceso de recuperación
			$resultado = array();
			
			// Valida los campos
			$reglas = array (
				array('field' => 'nom_re_usuario_txt', 'label' => 'Nombre usuario', 'rules' => 'trim|required'),
				array('field' => 'pass_ant_txt', 'label' => 'Contraseña', 'rules' => 'trim|required'),
				array('field' => 'pass_new_txt', 'label' => 'Nueva contraseña', 'rules' => 'trim|required|matches[pass_re_new_txt]|min_length[8]|max_length[16]'),
				array('field' => 'pass_re_new_txt', 'label' => 'Repetir contraseña', 'rules' => 'trim|required')
			);
			
			// Mensajes propios para los errores
			$this->form_validation->set_message("required", "- El campo %s es requerido");
			$this->form_validation->set_message("max_length", "- %s puede tener máximo %s caracteres");
			$this->form_validation->set_message("min_length", "- %s debe tener mínimo %s caracteres");
			$this->form_validation->set_message("matches", "- %s no coincide");

			$this->form_validation->set_rules($reglas);
			
			if($this->form_validation->run()) {
				// Intenta actualizar la contraseña
				$obj = new stdClass();
				$obj->nombre_usuario = $this->input->post("nom_re_usuario_txt");
				$obj->pass = md5($this->input->post("pass_ant_txt"));
				$obj->new_pass = md5($this->input->post("pass_new_txt"));
				
				$res = $this->usuario_modelo->recuperarPass($obj);
				
				switch($res) {
					case "si":
						$resultado = array("exito" => "si", "mensaje" => "Contraseña actualizada satisfactoriamente.");
						break;
					case "no":
						$resultado = array("exito" => "no", "mensaje" => "No se pudo actualizar la contraseña. Si el error persiste, por favor contáctate con el Administrador.");
						break;
					case "no_user":
						$resultado = array("exito" => "no", "mensaje" => "Este usuario y/o contraseña son incorrectos.");
						break;
					default:
						$resultado = array("exito" => "no", "mensaje" => "Error desconocido. Si persiste, por favor contáctate con el Administrador.");
						break;
				}
			} else {
				$resultado = array("exito" => "no", "mensaje" => validation_errors());
			}
			
			echo json_encode($resultado);
		} else {
			// Carga la vista para la recuperación de contraseñas
			// Levanta ventana
			$data["vista"] = "recuperar_pass_vista";
			$this->load->view("layout", $data);
		}
	}
}
?>