<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 20-03-2012
 | Nombre: noticia.php
 |
 | Descripción:	Controla las noticias
 | 
 | Cambios
 | -------
 
 */

class Noticia extends CI_Controller {

	function __construct() {
		parent::__construct();

		// Carga el modelo, conectándolo con la base de datos
		$this->load->model("noticia_modelo", "", TRUE);
	}
	
	
	function masInfo() {
		$resultado = array();
		
		$idNoticia = $this->input->post("id_noticia");
		$objNoticia = $this->noticia_modelo->masInfo($idNoticia);
		
		if(!is_null($objNoticia)) {
			$arrMes = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
			$microseg = strtotime($objNoticia->fecha);
			$mes = $arrMes[(int)date('m', $microseg)-1];
	
			$objNoticia->fecha = $mes." ".date('d', $microseg).", ".date('Y', $microseg)." a las ".date('H:i:s', $microseg);
			
			$resultado = array("exito" => "si", "resultado" => $objNoticia);
		} else {
			$resultado = array("exito" => "no", "mensaje" => "No se pudo cargar la noticia. Si el error persiste, contáctate con el Administrador");
		}
		
		echo json_encode($resultado);
	}
	
}
?>