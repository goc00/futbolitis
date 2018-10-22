<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 15-01-2012
 | Nombre: principal.php
 |
 | Descripción:	Controlador para la interfaz principal de
 | juego con la que interactúa el usuario. Contiene los
 | métodos principales de apuesta.
 |  
 |
 */

class Principal extends CI_Controller {

    private $diaInicioSemana;
    private $diaFinalSemana;
	
    function __construct() {
        parent::__construct();
        $this->diaInicioSemana = "";
        $this->diaFinalSemana = "";
		$this->load->model("core_modelo", "", TRUE);
        $this->load->model("opcion_modelo", "", TRUE);
		$this->load->model("apuesta_modelo", "", TRUE);
		$this->load->model("comentario_modelo", "", TRUE);
		$this->load->model("partido_modelo", "", TRUE);
		$this->load->model("constante_modelo", "", TRUE);
		$this->load->model("usuario_modelo", "", TRUE);
		$this->load->model("resultado_modelo", "", TRUE);
		$this->load->model("equipo_modelo", "", TRUE);
    }
	
	/* ---------------------------------------------------
	 | INTERFAZ DEL JUGADOR AL CARGAR EL CONTROLADOR
	 * --------------------------------------------------- */
	public function index() {
		$idUsuario = $this->session->userdata("id_usuario");
	
		if($idUsuario) {
			
			// Calcula el lunes y domingo de la semana
			$this->diasSemana();
			
			// Carga las constantes para el juego
			$this->cargarConstantes();
			
			// Arma toda la data que será pasada a la vista para su despliegue
			$data['vista'] = 'con_login_vista';
			
			// Formatea fecha y hora de partidos
			$partidos = $this->listaPartidos();
			foreach($partidos as $objPartido) {
				$objPartido->fechaFormat = $this->formatFechaPartido($objPartido->fecha_programada);
				$objPartido->horaFormat = $this->formatHoraPartido($objPartido->hora_programada);
			}
			$data['lista_partidos'] = $partidos;
			
			$data['opciones_apostar'] = $this->opcionesApostar();
			$data['apuestas_existentes'] = array();
			$data['inside'] = TRUE;
			$data['objPrimerPartido'] = NULL; // Esta variable controlará despliegue de interface o no
			$data['comentarios'] = array();

			// Busca si hay apuestas del jugador sobre el primer partido
			if(count($data['lista_partidos']) > 0) {				
				$data['objPrimerPartido'] = $data['lista_partidos'][0];
				$data['objPrimerPartido']->titulo = ($data['objPrimerPartido']->destacado) ? "Partido DESTACADO de la semana" : "Partido en la semana";
				
				foreach($data['opciones_apostar'] as $objOpcion) {
					$objApuestaExiste = $this->apuesta_modelo->buscaApuesta($data['objPrimerPartido']->id_partido, $idUsuario, $objOpcion->id_opcion);
					if($objApuestaExiste) {
						// Le pasa los id como índices, para así no tener que recorrer para buscar, sino,
						// saltar directamente al arreglo que contiene los valores de las apuestas
						$data['apuestas_existentes'][$data['objPrimerPartido']->id_partido][$objOpcion->id_opcion] = array
																								(
																									"resultado_local" => $objApuestaExiste->resultado_local,
																									"resultado_visita" => $objApuestaExiste->resultado_visita
																								);
					}
				}
				// Últimos 20 mensajes, además le da formato a la fecha
				$comentarios = $this->comentario_modelo->getComentarios($data['objPrimerPartido']->id_partido, $this->session->userdata["MAX_COMENTARIOS"]);
				foreach($comentarios as $objComentario) $objComentario->fecha = $this->mensajePorFecha($objComentario->fecha);
				$data['comentarios'] = $comentarios;
			}

			// Carga la vista, recibiendo toda la data creada
			$this->load->view("layout", $data);
		} else {
			// Si no está logeado, lo redirecciona a la página principal
			redirect(index_page());
		}
	}
	

	
	/* ---------------------------------------------------
	 | APUESTA SOBRE UN PARTIDO EN PARTICULAR
	 * --------------------------------------------------- */
	public function apostar() {
		$salida = "";
		$idUsuario = $this->session->userdata("id_usuario");
		
		// Valida que el usuario esté logeado
		if($idUsuario) {
	
			// Trae al objeto partido sobre el cual se apostará
			$idPartido = $this->input->post("numero_partido");
			$objPartido = $this->partido_modelo->getPartido($idPartido);
			
			// Verifica que esté dentro del tiempo permitido para apostar
			if(strtotime($objPartido->fecha_programada." ".$objPartido->hora_programada) > date(time())) {
			
				// Si hay opciones plus apostadas, verifica que tenga los créditos suficientes
				// ------
				// Recibe los inputs de apuesta
				parse_str($this->input->post("inputs"), $inputs);
				
				// Total de créditos del usuario guardados en la session
				$total_creditos = $this->session->userdata("total_creditos");
				
				// Sumará el total de créditos necesarios para las apuestas realizadas
				$suma = 0;
				
				// Todas las opciones del sistema
				$opciones = $this->opcion_modelo->getOpciones();
				
				$sinApuesta = TRUE;
				$arregloConApuestas = array();
				foreach($opciones as $opcion) {
					// Se consideran solo los inputs donde se hicieron apuestas (que fueron completados)
					// Esto quiere decir donde se haya completado tanto local como visita
					$vLocal = trim($inputs['opcion-A-'.$opcion->id_opcion]);
					$vVisita = trim($inputs['opcion-B-'.$opcion->id_opcion]);
					
					if($vLocal != "" && $vVisita != "") { 
						$sinApuesta = FALSE;		// Ya con 1 opción apostada basta
						
						// Si la opción es del tipo plus, verifica si existe o no ya una apuesta sobre esta
						// Si llega a existir, no debe volver a restarle los créditos
						if ($opcion->plus == 1)
							if(!$this->apuesta_modelo->apuestaExiste($idPartido, $idUsuario, $opcion->id_opcion))
								$suma += $opcion->costo;	// Calcula el total de créditos necesarios para apuestas del tipo plus
						
						
						// Almacena las apuestas del jugador
						$arregloConApuestas[] = array(	"id_opcion" => $opcion->id_opcion,
														"local" => $vLocal,
														"visita" => $vVisita);
					}
				}
				
				// SI HAY APUESTAS
				if(!$sinApuesta) {
					if($suma > 0 && $total_creditos < $suma) {
						// Si existen apuestas sobre opciones plus y no existen créditos suficientes
						$salida = array("exito" => "no", "mensaje" => "No posees suficientes créditos para realizar estas apuestas");
					} else {
						/* POR SEGURIDAD, vuelve a validar que el partido no esté cerrado. Alguien podría habilitar
						 * el botón de apuesta, por ejemplo con el firebug e igual generar una apuesta */
						// Inserta la apuesta, además actualiza el total de créditos del usuario
						if($this->apuesta_modelo->insertar($idPartido, $idUsuario, $arregloConApuestas, $suma)) {
							// Actualiza el valor dentro de la sesión
							$dif = 0;
							if($suma>0) {
								$dif = $this->session->userdata("total_creditos") - $suma;
								$this->session->set_userdata("total_creditos", $dif);
							}
							$salida = array("exito" => "si",
											"mensaje" => "¡Apuestas realizadas satisfactoriamente!",
											"diferencia" => $dif);
						} else {
							$salida = array("exito" => "no", "mensaje" => "Lo sentimos, no se completaron las apuestas sobre este partido");
						}
					}
				} else {
					$salida = array("exito" => "no",
									"mensaje" => "No has realizado apuesta alguna. Recuerda que para apostar sobre una opción,
												 debes completar el resultado tanto para local como visita.");
				}
			
			} else {
				$salida = array("exito" => "no", "mensaje" => "Ya no es posible realizar apuestas sobre este partido");
			}
		} else {
			$salida = array("exito" => "no", "mensaje" => "Debes autentificarte para poder realizar apuestas sobre este partido");
		}
		
		echo json_encode($salida);
	}
	
	function evaluaResultado($v1, $v2) {
		$res = '';
		
		if($v1 > $v2) {
			$res = 'L';
		} elseif($v1 < $v2) {
			$res = 'V';
		} else {
			$res = 'E';
		}
		
		return $res;
	}
	
	
	/* ***************************************************************************
	 * Busca apuestas sobre un partido en particular para el usuario autentificado
	 * *************************************************************************** */
	public function cargarPartido() {
		$apuestas = array();
		
		$idPartido = $this->input->post("idPartido");
		$idPartidoAnterior = $this->input->post("idPartidoAnterior");
		
		/*$idPartido = 1;
		$idPartidoAnterior = 2;*/
		
		// Objeto partido y sus objetos equipo
		$objPartido = $this->partido_modelo->getPartido($idPartido);
		$objPartido->nom_equipo_local = $this->equipo_modelo->getEquipo($objPartido->id_equipo_local)->nombre;
		$objPartido->nom_equipo_visita = $this->equipo_modelo->getEquipo($objPartido->id_equipo_visita)->nombre;
		
		$objPartidoAnterior = $this->partido_modelo->getPartido($idPartidoAnterior);
		$objPartidoAnterior->nom_equipo_local = $this->equipo_modelo->getEquipo($objPartidoAnterior->id_equipo_local)->nombre;
		$objPartidoAnterior->nom_equipo_visita = $this->equipo_modelo->getEquipo($objPartidoAnterior->id_equipo_visita)->nombre;
		
		// Formatea fecha y hora de los partidos
		$objPartido->fechaFormat = $this->formatFechaPartido($objPartido->fecha_programada);
		$objPartido->horaFormat = $this->formatHoraPartido($objPartido->hora_programada);
		$objPartidoAnterior->fechaFormat = $this->formatFechaPartido($objPartidoAnterior->fecha_programada);
		$objPartidoAnterior->horaFormat = $this->formatHoraPartido($objPartidoAnterior->hora_programada);
	
		// Título del partido
		$objPartido->titulo = ($objPartido->destacado) ? "Partido DESTACADO de la semana" : "Partido en la semana";
		
		// Listado de opciones
		$arrOpciones = $this->opcionesApostar();
		
		// ID del usuario
		$idUsuario = $this->session->userdata("id_usuario");
		
		// Comentarios del partido
		$comentarios = $this->comentario_modelo->getComentarios($idPartido, $this->session->userdata("MAX_COMENTARIOS"));
		foreach($comentarios as $objComentario) $objComentario->fecha = $this->mensajePorFecha($objComentario->fecha);
		
		// Genera un arreglo con todas las apuestas del partido
		foreach($arrOpciones as $objOpcion) {
			$objApuestaExiste = $this->apuesta_modelo->buscaApuesta($idPartido, $idUsuario, $objOpcion->id_opcion);
			if($objApuestaExiste) {
				$apuestas[] = array("id_opcion" => (int)$objOpcion->id_opcion,
									"resultado_local" => $objApuestaExiste->resultado_local,
									"resultado_visita" => $objApuestaExiste->resultado_visita);
			}
		}
		
		// Busca la fecha del servidor
		$horaServidor = date('Y-m-d H:i:s');
		
		echo json_encode(array(
								"apuestas" => $apuestas,
								"comentarios" => $comentarios,
								"horaServidor" => $horaServidor,
								"objPartido" => $objPartido,
								"objPartidoAnterior" => $objPartidoAnterior
							));
	}
	
	// Carga de constantes desde la base de datos
	function cargarConstantes() {
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
	}
	// Cálculo día de inicio y fin de la semana
    function diasSemana($formato = "Y-m-d") {
        $semana = date("W");
        $ano = date("Y");

        $primerDiaAno = mktime(0,0,0,1,1,$ano);                     // primer día del año
        $sabadoSemanaActual = $semana * 7 * 86400;					// horas * minutos * segundos = 86400 (1 día)
		$sabadoSemanaAnterior = ($semana-1) *7 * 86400;				// El sábado lo considera como fin de semana

        $this->diaInicioSemana = date($formato, $primerDiaAno + $sabadoSemanaAnterior + 86400*2);
        $this->diaFinalSemana = date($formato, $primerDiaAno + $sabadoSemanaActual + 86400);
    }
	
	// Formatea la fecha de los comentarios
	/*function formatoFechaComentario($fecha=NULL) {
		//$fecha = "2012-01-12 23:10:30";
		$meses = array("ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic");
		$fecha = date("Y-m-d H:i:s", strtotime($fecha));
		list($fechaA, $horaA) = explode(" ", $fecha);
		list($ano, $mes, $dia) = explode("-", $fechaA);
		$salida = $dia." ".$meses[(int)$mes-1].", ".$ano." a las ".$horaA;
		
		return $salida;
	}*/
	
	// Formato para la fecha de un partido
	function formatFechaPartido($fecha) {
		$dias = array("lun.", "mar.", "mié.", "jue.", "vie.", "sáb.", "dom");
		$dia = date("d", strtotime($fecha));
		$diaNum = (int)date("N", strtotime($fecha)) - 1;
		
		return $dias[$diaNum]." ".$dia;
	}
	function formatHoraPartido($fecha) {
		return date("H:i", strtotime($fecha));
	}
	
	
	// Formato para la fecha de un comentario
	function mensajePorFecha($fecha=null) {
		//$fecha = "2012-01-10 15:12:01";
		$formato = "Y-m-d H:i:s";
		$mensaje = "Hace {valor} {unidad}";
		// Todos los valores en función de segundos
		$segundo = 1;
		$minuto = $segundo * 60;
		$hora = $minuto * 60;
		$dia = $hora * 24;
		$mes = $dia * 30;	// Considera a los meses con 30 días
		$ano = $mes * 12;
		
		// formato fecha unix, SEGUNDOS
		$fechaActual = strtotime((string)date($formato));
		$fecha = strtotime($fecha);	// siempre será menor o igual a la fecha actual
		
		$diferencia = $fechaActual - $fecha;
		
		
		if($diferencia < $minuto) {
			// Segundos
			$mensaje = str_replace("{valor}", $diferencia, $mensaje);
			$mensaje = str_replace("{unidad}", "segundo(s)", $mensaje);
		} elseif ($diferencia >= $minuto && $diferencia < $hora) {
			// Minutos
			$valor = floor($diferencia/$minuto);
			$mensaje = str_replace("{valor}", $valor, $mensaje);
			$mensaje = str_replace("{unidad}", "minuto(s)", $mensaje);
		} elseif ($diferencia >= $hora && $diferencia < $dia) {
			// Horas
			$valor = floor($diferencia/$hora);
			$mensaje = str_replace("{valor}", $valor, $mensaje);
			$mensaje = str_replace("{unidad}", "hora(s)", $mensaje);
		} elseif ($diferencia >= $dia && $diferencia < $mes) {
			// Días
			$valor = floor($diferencia/$dia);
			$mensaje = str_replace("{valor}", $valor, $mensaje);
			$mensaje = str_replace("{unidad}", "día(s)", $mensaje);
		} elseif ($diferencia >= $mes && $diferencia < $ano) {
			// Meses
			$valor = floor($diferencia/$mes);
			$mensaje = str_replace("{valor}", $valor, $mensaje);
			$mensaje = str_replace("{unidad}", "mes(es)", $mensaje);
		} elseif ($diferencia >= $ano) {
			// Años
			$valor = floor($diferencia/$ano);
			$mensaje = str_replace("{valor}", $valor, $mensaje);
			$mensaje = str_replace("{unidad}", "año(s)", $mensaje);
		}
		
		return $mensaje;
	}
	
	
	// Listar partidos de la semana, se llama desde el modelo CORE porque involucra llamados a otras tablas
    function listaPartidos() { return $this->core_modelo->getListaPartidos($this->diaInicioSemana, $this->diaFinalSemana); }
	
	// Opciones sobre las cuales se podrá apostar
    function opcionesApostar() { return $this->opcion_modelo->getOpciones(); }
	
	// Devuelve la hora del servidor en formato JSON
	function horaServidor() { echo json_encode(array("resultado" => date('Y-m-d H:i:s'))); }
}