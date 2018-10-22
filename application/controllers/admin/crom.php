<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crom extends CI_Controller {
	
	private $diaInicioSemana;
    private $diaFinalSemana;

    function __construct() {
        parent::__construct();

        $this->load->model("opcion_modelo", "", TRUE);
		$this->load->model("apuesta_modelo", "", TRUE);
		$this->load->model("partido_modelo", "", TRUE);
		$this->load->model("constante_modelo", "", TRUE);
		$this->load->model("usuario_modelo", "", TRUE);
		$this->load->model("resultado_modelo", "", TRUE);
    }
	
	public function index() {
		$idUsuario = $this->session->userdata("id_usuario");
		if(!$idUsuario) echo 'Acceso restringido';
	}
	
	/* ---------------------------------------------------------------------------
	 | CALCULARÁ EL PUNTAJE DE LOS USUARIOS POR SEMANA
	 * --------------------------------------------------------------------------- */
	public function puntajes() {
		$constantes = array();
		$usuarios = array();
		$partidos = array();
		$opciones = array();
		
		// Carga las constantes
		$arrConstantes = $this->constante_modelo->getConstantes();
		foreach($arrConstantes as $objConstante) $constantes[$objConstante->nombre] = $objConstante->valor;
	
		// Carga días de inicio y fin de la semana
		$this->diasSemana();
		
		// Trae a todos los usuarios ACTIVOS y los partidos de la semana
		// Pasa como índice los ID para seleccionar inmediatamente el arreglo
		// y no tener que recorrerlo para encontrar la información. Mucho más eficiente
		$usuariosBD = $this->usuario_modelo->usuariosActivos($constantes['MAX_MESES_USUARIO_ACTIVO']);
		foreach($usuariosBD as $objUsuarioBD)
			$usuarios[$objUsuarioBD->id_usuario] = $objUsuarioBD;
		
		$partidosBD = $this->partido_modelo->getPartidos($this->diaInicioSemana, $this->diaFinalSemana);
		foreach($partidosBD as $objPartidoBD) {
			$idPartidoBD = $objPartidoBD->id_partido;
			$resSemana = $this->resultado_modelo->getResultados($idPartidoBD);
			$objPartidoBD->resultado = array();
			
			// Recorre los resultados
			foreach($resSemana as $objResultado) {
				// PARTIDO_OPCION
				$indice = $idPartidoBD."_".$objResultado->id_opcion;
				$objPartidoBD->resultado[$indice] = $objResultado;
			}
			
			// Toma los resultados de la opción principal, para calcular:
			// L = Local
			// E = Empate
			// V = Visita
			// 0 = No aplica
			if(count($objPartidoBD->resultado) > 0) {
				// Si hay resultados, verifica que exista el resultado con el índice seleccionado
				if(isset($objPartidoBD->resultado[$idPartidoBD."_".$constantes['OPCION_PRINCIPAL']])) {
					$op = $objPartidoBD->resultado[$idPartidoBD."_".$constantes['OPCION_PRINCIPAL']];
					$objPartidoBD->res = $this->evaluaResultado($op->resultado_local, $op->resultado_visita);
				} else {
					$objPartidoBD->res = '0';
				}
			} else {
				$objPartidoBD->res = '0';
			}
			
			// Almacena entonces los partidos con sus resultados
			$partidos[$objPartidoBD->id_partido] = $objPartidoBD;
		}
		
		// Opciones con su ponderación
		$opcionesBD = $this->opcionesApostar();
		foreach($opcionesBD as $objOpcion) {
			// OPCION
			$opciones[$objOpcion->id_opcion] = $objOpcion;
		}
		
		// Apuestas realizadas por el usuario sobre los partidos de la semana
		foreach($usuarios as $objUsuario) {
			$total = 0;
			foreach($partidos as $objPartido) {
				$apuestasTmp = $this->apuesta_modelo->getApuestasUP($objUsuario->id_usuario, $objPartido->id_partido);
				
				// Si no posee puntaje, le asigna inmediatamente 0
				if(count($apuestasTmp) > 0) {
				
				
					// ***** CALCULA EL PUNTAJE PARA EL JUGADOR *****
					foreach($apuestasTmp as $objApuesta) {
						// Ponderación de la opción
						$ponderacion = (float)$opciones[$objApuesta->id_opcion]->ponderacion;
						
						// Verifica que exista un resultado para comparar con la apuesta
						if(isset($objPartido->resultado[$objPartido->id_partido."_".$objApuesta->id_opcion])) {
							// Retorna un objeto con el resultado de un partido sobre una opción
							$resDePartido = $objPartido->resultado[$objPartido->id_partido."_".$objApuesta->id_opcion];
							
							// Verifica si hay bonus por acertarle a la opción principal, claro que si el usuario apostó sobre esta
							$bonus = 1;
							if($objApuesta->id_opcion == $constantes['OPCION_PRINCIPAL'] && $objPartido->res != 0) {
								$result = $this->evaluaResultado($objApuesta->resultado_local, $objApuesta->resultado_visita);
								if($result == $objPartido->res)
									$bonus += ($constantes['PORCENTAJE_BONUS']/100);
							}
							
							// CALCULA ACIERTO DE LA APUESTA Y CERCANÍA DE ESTA
							// Calcula el intervalo de porcentaje que se considerará hacia arriba y abajo
							// sobre el valor de la ponderación, acierto perfecto significa 100% del total
							// de la ponderación
							$porcionPorcLocal = 100/($resDePartido->resultado_local+1);
							$porcionPorcVisita = 100/($resDePartido->resultado_visita+1);
							
							// Calcula el porcentaje que le corresponde de la ponderación
							$dif = $resDePartido->resultado_local-$objApuesta->resultado_local;
							$dif = ($dif < 0) ? $dif*-1 : $dif;
							$porcL = (($porcionPorcLocal*$dif) < 100) ? 100-($porcionPorcLocal*$dif) : 0;
							
							
							
							$dif = $resDePartido->resultado_visita-$objApuesta->resultado_visita;
							$dif = ($dif < 0) ? $dif*-1 : $dif;
							$porcV = (($porcionPorcVisita*$dif) < 100) ? 100-($porcionPorcVisita*$dif) : 0;
							
							// La ponderación aplicándole el porcentaje de acierto
							$ponderacionL = round($ponderacion * ($porcL/100), 4);
							$ponderacionV = round($ponderacion * ($porcV/100), 4);	
										
							$total += ($ponderacionL + $ponderacionV) * $bonus;
						} else {
							// Si por algún motivo no está definido el resultado, se le asigna el 100% de la ponderación
							// del valor al usuario, esta situación NUNCA DEBERÍA OCURRIR, pero se controla por si acaso.
							// Además, si resulta que correspondía a la opción principal, le agrega el bonus
							$bonus = 1;
							if($objApuesta->id_opcion == $constantes['OPCION_PRINCIPAL']) {
								$bonus += ($constantes['PORCENTAJE_BONUS']/100);
							}	
							$total += ($ponderacion*2) * $bonus;
						}

					}
					
					
				}
			}
			// Asigna finalmente el puntaje al jugador
			$objUsuario->puntaje = $total;
		}
		echo '<pre>';
		print_r($usuarios);
		echo '</pre>';
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
	
	
	// Cálculo día de inicio y fin de la semana
    function diasSemana($formato = "Y-m-d") {
        $semana = date("W");
        $ano = date("Y");

        $primerDiaAno = mktime(0,0,0,1,1,$ano);                     // primer día del año
        $domingoSemanaAnterior = (($semana-1)*7)+1;                 // domingo de la semana anterior
        $domingoSemanaActual = ($semana*7) * 86400;            		// domingo de la semana actual
        $lunesSemanaActual = $domingoSemanaAnterior * 86400;    	// dias * (horas * minutos * segundos)

        $this->diaInicioSemana = date($formato, $primerDiaAno + $lunesSemanaActual);
        $this->diaFinalSemana = date($formato, $primerDiaAno + $domingoSemanaActual);
    }
	
	// Opciones sobre las cuales se podrá apostar
    function opcionesApostar() { return $this->opcion_modelo->getOpciones(); }
}