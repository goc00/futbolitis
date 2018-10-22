<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 15-01-2012
 |
 | Descripción:	Despliega el ránking actual del juego
 |
 | Cambios: Ninguno
 |
 */

class Ranking extends CI_Controller {

	private $prueba;

	function __construct() {
		parent::__construct();
		
		// Carga modelo
		$this->load->model("ranking_modelo", "", TRUE);
	}
	
	/* Función de inicio del controlador */
	function index() {
		$idUsuario = $this->session->userdata("id_usuario");

		if($idUsuario) {				
			// Trae los 10 primeros lugares (por defecto)
			$pos = $this->armaRanking($idUsuario);
			$data['vista'] = 'ranking_vista';
			$data['inside'] = TRUE;
			$data['posiciones'] = $pos;
			
			// Carga la vista pasándole el arreglo de objetos
			$this->load->view('layout', $data);
		} else {
			// Si no está logeado, lo redirecciona a la página principal
			redirect(index_page());
		}
	}
	
	function armaRanking($idUsuario, $desde=0, $numero=10) {
		$res = $this->ranking_modelo->getPosiciones($desde, $numero);
			
		// Evalúa, asignando posiciones y buscando al jugador actual
		if(count($res) > 0) {
			$posicion = 1+$desde;
		
			foreach($res as $obj) {
				// Asigna posición
				$obj->posicion = $posicion;
				$obj->marcar = FALSE;
				$posicion++;
				
				// Verifica si el usuario está dentro de los registros buscados
				if($obj->id_usuario == $idUsuario) $obj->marcar = TRUE;
			}
		}
		
		return $res;
	}
	
	// Buscar posición del jugador con respecto a los demás
	function posicionJugador($idUsuario) {
	}
}

/* End of file Ranking.php */