<?php
	header("content-type: application/x-javascript");
	
	/* ******************************************
	 * CONSTANTES CARGADAS DESDE LA BASE DE DATOS
	 * AUTOR: GASTÓN ORELLANA C.
	 * FECHA: 11-01-2012
	 * ****************************************** */
	 
	if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	class Own extends CI_Controller {
	
		private $prueba;
	
		function __construct() {
			parent::__construct();
			
			// Llama al modelo que trae todas las contantes desde la base de datos
			$this->load->model("constante_modelo", "", TRUE);
		}
		
		/* CARGA TODAS LAS CONSTANTES DESDE LA BASE DE DATOS */
		function index() {
			
			// Carga las constantes
			if(!$this->session->userdata('constantes_on')) {
				// Flag para verificar si las constantes están cargadas
				$this->session->set_userdata('constantes_on', 1);
				
				// Carga las constantes
				$arreglo = array();
				$constantes = $this->constante_modelo->getConstantes();
				foreach($constantes as $objConstante) $arreglo[$objConstante->nombre] = $objConstante->valor;
				
				// IMPORTANTE: Deja cada constante como una variable de session
				$this->session->set_userdata($arreglo);
			}
				
			echo 'var arrObjPartido = new Array();';						// CONTENDRÁ TODOS LOS OBJETOS PARTIDO PARA PODER MANIPULARLOS CON JQUERY
			echo 'var timer;';												// Tiempo que se visualiza en la parte central
			echo 'var bloqueo = false;';									// Controla la repitición de clicks sobre las filas para que no repliquen
			echo 'var sitio = "'.base_url().'";';							// Ruta del sitio
			
			// VARIABLES PARA EL CONTROL DE LOS COMENTARIOS
			echo 'var maxChars = '.$this->session->userdata('MAX_CHARS').';';				// Máximo de caracteres por mensaje
		}
	}

/* End of file Own.php */