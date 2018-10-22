<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 15-01-2012
 |
 | Descripción:	Muestra todos los resultados de los partidos
 | anteriores. Sirve de información histórica para los usuarios
 | que desean verificar su puntaje actual o cualquier tema
 | relacionado a este.
 |
 | Cambios: Ninguno
 |
 */

class Pasado extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		// Carga modelo
		$this->load->model("core_modelo", "", TRUE);
		$this->load->model("resultado_modelo", "", TRUE);
		$this->load->model("opcion_modelo", "", TRUE);
	}
	
	/* Función de inicio del controlador */
	function index() {
		$this->conjunto = array();
		$idUsuario = $this->session->userdata("id_usuario");

		if($idUsuario) {
			// Partidos anteriores desde el inicio del sitio hasta el día
			// anterior al actual (pasar como constante?????)
			$diasAnteriores = 1;
			$fechaInicio = '2011-01-01';
			$fechaFin = date('Y-m-d', strtotime('-1 day'));
			$destacados = array();
			$porFecha = array();
			$conjunto = array();
			$ultimaFecha = '';
			$partidos = $this->core_modelo->getListaPartidos($fechaInicio, $fechaFin);
			
			// Agrupa en primera instancia por destacado, luego por día
			foreach($partidos as $objPartido) {
				if((int)$objPartido->destacado == 1) {
					// Agrega al grupo de partidos destacados
					array_push($destacados, $objPartido);
				} else {
					// Va agrupando por fecha
					if($ultimaFecha != $objPartido->fecha_programada) {
						// Limpia el arreglo para agregar nuevo conjunto
						$conjunto = array();
						
						// Arma objeto y agrega al conjunto
						array_push($conjunto, $objPartido);
						$porFecha[$objPartido->fecha_programada] = $conjunto;
						
						// Deja la última fecha
						$ultimaFecha = $objPartido->fecha_programada;
					} else {
						$porFecha[$objPartido->fecha_programada][] = $objPartido;
					}
				}
			}
			
			$data['partidos_agrupados'] = array('destacados' => $destacados, 'por_fecha' => $porFecha);
			$data['vista'] = 'pasado_vista';
			$data['inside'] = TRUE;
			
			// Carga la vista pasándole el arreglo de objetos
			$this->load->view('layout', $data);
		} else {
			// Si no está logeado, lo redirecciona a la página principal
			redirect(index_page());
		}
	}
	
	// Carga los resultados del partido
	function muestraResultados() {
		$res = "";
		$idPartido = $this->input->post('id_partido');
		//$idPartido = 4;
		
		if($idPartido) {
			// Si llega el ID del partido, trae sus resultados
			$resultados = $this->resultado_modelo->getResultados($idPartido);
			
			// Prepara información para devolverla
			// Busca el nombre de las opciones
			foreach($resultados as $objResultado) {
				$ev = $this->opcion_modelo->getOpcion($objResultado->id_opcion);
				$objResultado->nombre_opcion = !is_null($ev) ? $ev->nombre : "Sin nombre";
			}
			$res = array("exito" => "si", "devolucion" => $resultados);
		} else {
			$res = array("exito" => "no", "devolucion" => "Partido desconocido");
		}
		
		/*echo "<pre>";
		print_r($res);
		echo "</pre>";*/
		echo json_encode($res);
	}
}

/* End of file pasado.php */