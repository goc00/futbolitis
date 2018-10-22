<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 20-02-2012
 | Nombre: comprar.php
 |
 | Descripción:	Clase controlador para la compra de créditos
 |
 | Cambios: Ninguno
 |
 */

class Comprar extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		// Carga modelo
		$this->load->model("comprar_modelo", "", TRUE);
	}
	
	/* Función de inicio del controlador */
	function index() {
		$idUsuario = $this->session->userdata("id_usuario");

		if($idUsuario) {

			$bolsas = $this->comprar_modelo->getBolsas();
			
			foreach($bolsas as $objBolsa) {
				// Crea hash para codificar información
				// ID_USUARIO + número random + ID_BOLSA + VALOR
				list($usec, $sec) = explode(' ', microtime());
				$semilla = (float) $sec + ((float) $usec * 100000);
				srand($semilla);
				$nRandom = rand();
				srand(); // resetea la semilla
				
				$hash = $idUsuario."+".$nRandom."+".$objBolsa->id_bolsa."+".$objBolsa->precio;
				$hash = str_replace("/", "", $hash);	// quita / para evitar problemas dentro del URI
				$objBolsa->hash = $this->encrypt->encode($hash);
			}
			
			$data['inside'] = TRUE; // Marca para cargar menú interno
			$data['vista'] = 'comprar_vista';
			$data['bolsas'] = $bolsas;
			
			// Carga la vista pasándole el arreglo de objetos
			$this->load->view('layout', $data);
		} else {
			// Si no está logeado, lo redirecciona a la página principal
			redirect(index_page());
		}
	}
	
	// Llega la información codificada para proteger los datos
	// ID_USUARIO + número random + ID_BOLSA
	function buy() {
		$resultado = array();
		$hash = $this->input->post("hash");
		
		$decode = $this->encrypt->decode($hash);
		list($idUsuario, $nRandom, $idBolsa, $precio) = explode("+", $decode);
		
		// Registra la compra
		$obj = new stdClass();
		$obj->id_usuario = $idUsuario;
		$obj->id_bolsa = $idBolsa;
		$obj->valor = $precio;
		
		if($this->comprar_modelo->comprar($obj)) $resultado = array("exito" => "si", "mensaje" => "Compra realizada satisfactoriamente.");
		else $resultado = array("exito" => "no", "mensaje" => "No se pudo generar la compra. Si el error persiste, por favor contáctate con el Administrador.");
		
		echo json_encode($resultado);
	}
}

/* End of file pasado.php */