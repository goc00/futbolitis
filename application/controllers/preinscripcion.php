<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* ----------------------------------------------------------
 | FUTBOLITIS
 | ----------------------------------------------------------
 |
 | Autor: GASTÓN ORELLANA C.
 | Fecha: 05-03-2012
 | Nombre: preinscripcion.php
 |
 | Descripción:	Posee métodos para completar los datos del usuario
 | luego de detectar que es del tipo pre-inscrito.
 | 
 | Cambios
 | -------
 |	Ninguno
 
 */

class Preinscripcion extends CI_Controller {

	function __construct() {
		parent::__construct();

		// Carga el modelo, conectándolo con la base de datos
		$this->load->model("direccion_modelo", "", TRUE);
		$this->load->model("persona_modelo", "", TRUE);
		$this->load->model("usuario_modelo", "", TRUE);
	}
	
	// Método inicial, recibe el id del usuario para completar información
	function go($idUsuario="") {
		if($idUsuario != "") {			
			// Carga inicialmente las regiones para el formulario
			$regiones = $this->direccion_modelo->getRegiones();
			$data["info"] = array("regiones" => $regiones, "id_usuario" => $idUsuario);
			$data["vista"] = "preinscripcion_vista";
			
			$this->load->view("layout", $data);
		} else {
			// No viene definido, redirecciona a página principal
			redirect(index_page());
		}
	}
	
	function completar() {
		$salida = array();
		
		// Validaciones para el formulario
		// alpha_dash -> Caracteres alfanuméricos, - y _		
	    $reglas = array (
				array('field' => 'nombre_txt', 'label' => 'Nombre usuario', 'rules' => 'trim|required|max_length[45]'),
				array('field' => 'ape_pat_txt', 'label' => 'Apellido paterno', 'rules' => 'trim|required|max_length[45]'),
				array('field' => 'ape_mat_txt', 'label' => 'Apellido materno', 'rules' => 'trim|required|max_length[45]'),
				array('field' => 'region_cmb', 'label' => 'Región', 'rules' => 'trim|required|numeric'),
				array('field' => 'provincia_cmb', 'label' => 'Provincia', 'rules' => 'trim|required|numeric'),
				array('field' => 'comuna_cmb', 'label' => 'Comuna', 'rules' => 'trim|required|numeric'),
               
                array('field' => 'dire_txt', 'label' => 'Dirección', 'rules' => 'trim|required|max_length[45]'),
				array('field' => 'num_dire_txt', 'label' => 'Número', 'rules' => 'trim|required|max_length[10]|numeric'),
	
				array('field' => 'dig_tel_txt', 'label' => 'Cód. área', 'rules' => 'trim|required|numeric|max_length[3]'),
				array('field' => 'tel_txt', 'label' => 'Teléfono', 'rules' => 'trim|required|numeric|max_length[7]'),
				
				array('field' => 'cel_txt', 'label' => 'Celular', 'rules' => 'trim|numeric|max_length[8]')
			);
			
        // Mensajes propios para los errores
        $this->form_validation->set_message("required", "- El campo %s es requerido");
        $this->form_validation->set_message("max_length", "- %s puede tener máximo %s caracteres");
        $this->form_validation->set_message("numeric", "- %s solo permite números");

        // Seteo de reglas
        $this->form_validation->set_rules($reglas);

        // Verifica si pasarán o no todas las variables
        if($this->form_validation->run()) {
			// Actualiza los datos del jugador
			// Crea objeto
			// Recibe el hash con el nombre de usuario + id usuario + id persona
			$hash = $this->encrypt->decode($this->input->post("hash"));
			list($nomUsuario, $idUsuario, $idPersona) = explode("+", $hash);
			
			$d1 = $this->input->post("dire_txt");
			$d2 = $this->input->post("tipo_dire_txt");
			$d3 = $this->input->post("num_dire_txt");
			$direccion = $d1.", ".$d2." #".$d3;
			
			$nT1 = $this->input->post("cod_tel_txt");
			$nT2 = $this->input->post("dig_tel_txt");
			$nT3 = $this->input->post("tel_txt");
			$telefono = $nT1."-(".$nT2.")-".$nT3;
			
			// Celular no es obligatorio
			$celular = NULL;
			if($this->input->post("cel_txt") != "") {
				$nC1 = $this->input->post("cod_cel_txt");
				$nC2 = $this->input->post("dig_cel_txt");
				$nC3 = $this->input->post("cel_txt");
				$celular = $nC1."-(".$nC2.")-".$nC3;
			}			
			
			$obj = new stdClass();
			$obj->id_usuario = $idUsuario;
			$obj->id_persona = $idPersona;
			$obj->nombre = $this->input->post("nombre_txt");
			$obj->apellido_paterno = $this->input->post("ape_pat_txt");
			$obj->apellido_materno = $this->input->post("ape_mat_txt");
			$obj->direccion = $direccion;
			$obj->pais = $this->input->post("pais_cmb");
			$obj->region = $this->input->post("region_cmb");
			$obj->provincia = $this->input->post("provincia_cmb");
			$obj->comuna = $this->input->post("comuna_cmb");
			$obj->telefono_particular = $telefono;
			$obj->telefono_celular = $celular;
			
			if($this->persona_modelo->actualiza($obj)) {
				// Si está OK, aparte de actualizar, modifica la fecha de ingreso del jugador y crea la sesión
				// Va a buscar los datos desde la tabla usuario
				$objUsuario = $this->usuario_modelo->getUsuario($idUsuario);
				$this->usuario_modelo->actualizaAcceso($idUsuario);
				
				$nuevaInfo = array(
									"id_usuario" => $obj->id_usuario,
									"id_persona" => $obj->id_persona,
									"id_estado" => $objUsuario->id_estado,
									"nombre_usuario" => $objUsuario->nombre_usuario,
									"email" => $objUsuario->email,
									"total_creditos" => $objUsuario->total_creditos,
									"ultimo_acceso" => $objUsuario->ultimo_acceso
								);

				$this->session->set_userdata($nuevaInfo);
				
				$salida = array("exito" => "si", "mensaje" => "Datos actualizados correctamente, ¡ya puedes comenzar a jugar!");
			} else {
				$salida = array("exito" => "no", "mensaje" => "No se pudieron actualizar tus datos. Si el error persiste, contáctate con el Administrador.");
			}
		} else {
			// No pasaron las validaciones
			$salida = array("exito" => "no", "mensaje" => validation_errors());
		}
		
		
		
		
		
		echo json_encode($salida);
	}
}
?>