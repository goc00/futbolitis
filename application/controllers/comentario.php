<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 14-02-2012
 |
 |
 */

class Comentario extends CI_Controller {

    function __construct() {
        parent::__construct();
		$this->load->model("comentario_modelo", "", TRUE);
		$this->load->model("partido_modelo", "", TRUE);
    }
	
	public function index() {
		$idUsuario = $this->session->userdata("id_usuario");
	
		if(!$idUsuario) {
			// Si no está logeado, lo redirecciona a la página principal
			redirect(index_page());
		}
	}
	
	// Almacena el comentario, controla además flooding
	public function escribirComentario() {
		$resultado = NULL;
		
		$idUsuario = $this->session->userdata("id_usuario");
		$idPartido = $this->input->post("id_partido");
		$mensaje = $this->input->post("caja_mensaje");
		
		if($mensaje != "") {
			if($idPartido && $idUsuario) {
			
				// Verifica si el partido todavía está abierto o no
				$objPartido = $this->partido_modelo->getValorCampo($idPartido, array("fecha_programada", "hora_programada"));
				
				if(!is_null($objPartido)) {
					// Calcula la diferencia de horario
					if(strtotime($objPartido->fecha_programada." ".$objPartido->hora_programada) > date(time())) {
					
						// Controla el flooding
						$objComentarioAlt = $this->comentario_modelo->getFechaUltimoComentario($idPartido, $idUsuario);
						
						if(!is_null($objComentarioAlt)) {
							// Puede venir sin registro, por ende, no hay último mensaje así que no pasa por el filtro de flooding
							if(!empty($objComentarioAlt)) {
							
								// Obtiene la diferencia
								$dif = time() - strtotime($objComentarioAlt->fecha);
								if($dif > (int)$this->session->userdata('SEG_EVITA_FLOOD')) {
									$resultado = $this->insertarComentario($idPartido, $idUsuario, $mensaje);
								} else {
									$delta = (int)$this->session->userdata('SEG_EVITA_FLOOD')-$dif;
									$resultado = array("resultado" => "no", "mensaje" => "Debes esperar $delta segundos para volver a escribir otro comentario");
								}
								
							} else {
								// Sin filtrar por flooding
								$resultado = $this->insertarComentario($idPartido, $idUsuario, $mensaje);
							}
						}
						
					} else {
						$resultado = array("resultado" => "no", "mensaje" => "No se permiten más comentarios para este partido");
					}
				}
			} else {
				$resultado = array("resultado" => "no", "mensaje" => "No están definidos el partido o el usuario");
			}
		} else {
			$resultado = array("resultado" => "no", "mensaje" => "No has escrito mensaje alguno para este partido");
		}
			
		echo json_encode($resultado);
	}
	function insertarComentario($idPartido, $idUsuario, $mensaje) {
		$resultado = NULL;
		
		$objComentario = $this->comentario_modelo->insertar($idPartido, $idUsuario, $mensaje);
		
		if($objComentario) $resultado = array("resultado" => "si", "mensaje" => "Muchas gracias por tu comentario. Prontamente será evaluado para su publicación");
		else $resultado = array("resultado" => "no", "mensaje" => "No se pudo insertar el mensaje");
		
		return $resultado;
	}

}