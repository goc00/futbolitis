<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 21-01-2012
 | Nombre: core.php
 |
 | Descripción:	Página de inicio del sitio, carga noticias,
 | calugas de Facebook y últimos resultados entre otros.
 | Levanta el contenido inicial
 |
 | Cambios: Ninguno
 |
 */

class Core extends CI_Controller {

    function __construct() {
        parent::__construct();
		$this->load->model("core_modelo", "", TRUE);
		$this->load->model("noticia_modelo", "", TRUE);
		$this->load->model("usuario_modelo", "", TRUE);
		$this->load->model("apuesta_modelo", "", TRUE);
    }

	public function index() {
		date_default_timezone_set("America/Santiago");
	
		// Obtiene los partidos de la semana
		$formato = "Y-m-d";
		$semana = date("W");
        $ano = date("Y");

        $primerDiaAno = mktime(0,0,0,1,1,$ano);                     // primer día del año
        $sabadoSemanaActual = $semana * 7 * 86400;					// horas * minutos * segundos = 86400 (1 día)
		$sabadoSemanaAnterior = ($semana-1) *7 * 86400;				// El sábado lo considera como fin de semana

        $lunesNextW = date($formato, $primerDiaAno + $sabadoSemanaAnterior + 86400*2);
        $domingoNextW = date($formato, $primerDiaAno + $sabadoSemanaActual + 86400);

        // Sin usuario logeado
        $data["vista"] = "sin_login_vista";
		// Trae los partidos de la próxima semana
		$data["partidos_next_week"] = $this->core_modelo->getListaPartidos($lunesNextW, $domingoNextW);
		// Noticias, las procesa para sacar un resumen automático
		$news = $this->noticia_modelo->getNoticias();
		$numCharsResumen = 70;
		$arrMes = array("ene.","feb.","mar.","abr.","may.","jun.","jul.","ago.","sep.","oct.","nov.","dic.");
		foreach($news as $objNews) {
			$objNews->resumen = substr($objNews->contenido, 0, $numCharsResumen) . "...";
			$microseg = strtotime($objNews->fecha);
			$mes = $arrMes[(int)date('m', $microseg)-1];
	
			$objNews->fecha = $mes." ".date('d', $microseg).", ".date('Y', $microseg);
		}
		$data["noticias"] = $news;
		$data["noticia_destacada"] = $this->noticia_modelo->noticiaDestacada();
		
		// Formatea la hora y fecha
		foreach($data["partidos_next_week"] as $objPartido) {
			$objPartido->lugar = $objPartido->lugar . ", " . $this->format($objPartido->fecha_programada) . " a las " . $objPartido->hora_programada;
		}
		
		$data["usuarios_registrados"] = $this->usuario_modelo->totalUsuarios();
		$data["usuarios_online"] = $this->usuariosOnline();
		$data["total_apuestas"] = $this->apuesta_modelo->totalApuestas();
		
		
        $this->load->view("layout", $data);
	}
	
	function format($fecha) {
		$dias = array("lunes", "martes", "miércoles", "jueves", "viernes", "sábado", "domingo");
		
		// Determina si el año es bisiesto o no, porque esto afecta en el cálculo
		//$esBisiesto = date('L', strtotime($fecha));
		
		$salida = (string)$dias[(int)date('N', strtotime($fecha))-1] . " " . date('d', strtotime($fecha));
		
		return $salida;
	}
	
	function usuariosOnline() {
		// Calcula estadísticas de usuarios en línea
		$ip = $_SERVER['REMOTE_ADDR'];
		
		// Definimos el momento actual
		$ahora = time();

		// Borrando los registros de las ip inactivas (30 minutos)
		$minSet = $this->session->userdata('MINUTOS_USUARIOS_ACTIVOS');
		$limite = $ahora-$minSet*60;
		$this->usuario_modelo->borrarControlIP($limite);

		// Revisa si el ip del visitante existe en la tabla
		if($this->usuario_modelo->existeIP($ip)) {
			// Si existe, actualiza fecha
			$this->usuario_modelo->actualizaIP($ahora, $ip);
		} else {
			// Si no existe, inserta
			$this->usuario_modelo->insertaIP($ahora, $ip);
		}

		// Cuenta número de IP
		return $this->usuario_modelo->totalIP();
	}

}