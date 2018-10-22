<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registro extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("usuario_modelo", "", TRUE);
		$this->load->model("direccion_modelo", "", TRUE);
    }

	public function index() {
		$data["regiones"] = $this->direccion_modelo->getRegiones();
		$data["vista"] = "registro_vista";
        $this->load->view("layout", $data);
		
        // Si hay un usuario logeado, lo redirecciona al index
        if($this->session->userdata("id_usuario")) {
            redirect(index_page());
        }
	}

	// Carga los datos de los campos
	function cargarCombos() {
		$tabla = $this->input->post("tabla");
		$valor = $this->input->post("valor");
		
		// Solo hace algo si le llega un ID
		if($valor != "") {
			
			// Va a buscar los datos a la tabla que corresponda
			$datosParaLlenar = $this->direccion_modelo->paraLlenar($valor, $tabla);
		
			if(!is_null($datosParaLlenar)) $resultado = array("exito" => "si", "datosParaLlenar" => $datosParaLlenar);
			else $resultado = array("exito" => "no", "mensaje" => "No se pudieron obtener los datos");
			
		} else {
			$resultado = array("exito" => "neutro");
		}
		
		echo json_encode($resultado);
	}
	
    public function registrar() {
		// Variable de salida que será encoded en JSON
		$salida = array();

        // Reglas para la validación del formulario de registro
        // alpha_dash -> Caracteres alfanuméricos, - y _
	    $reglas = array (
				array('field' => 'reg_nombre_usuario_txt', 'label' => 'Nombre usuario', 'rules' => 'trim|required|alpha_dash|max_length[45]'),
				array('field' => 'reg_contrasena_txt', 'label' => 'Contraseña', 'rules' => 'trim|required|matches[reg_re_contrasena_txt]|min_length[8]|max_length[128]'),
				array('field' => 'reg_re_contrasena_txt', 'label' => 'Repetir contraseña', 'rules' => 'trim|required'),
				array('field' => 'reg_email_txt', 'label' => 'E-mail', 'rules' => 'trim|required|valid_email|matches[reg_re_email_txt]'),
				array('field' => 'reg_re_email_txt', 'label' => 'Repetir e-mail', 'rules' => 'trim|required'),
                // Datos personales
				array('field' => 'reg_nombre_txt', 'label' => 'Nombre', 'rules' => 'trim|required|max_length[45]'),
                array('field' => 'reg_apellido_pat_txt','label' => 'Apellido paterno','rules' => 'trim|required|max_length[45]'),
                array('field' => 'reg_apellido_mat_txt', 'label' => 'Apellido materno', 'rules' => 'trim|required|max_length[45]'),

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
        $this->form_validation->set_message("min_length", "- %s debe tener mínimo %s caracteres");
        $this->form_validation->set_message("matches", "- %s no coincide");
        $this->form_validation->set_message("alpha_dash", "- El campo %s solo permite caracteres alfanuméricos, - y _");
        $this->form_validation->set_message("numeric", "- %s solo permite números");
		$this->form_validation->set_message("valid_email", "- La dirección de correo es inválida");

        // Seteo de reglas
        $this->form_validation->set_rules($reglas);

        // Verifica si pasarán o no todas las variables
        if($this->form_validation->run()) {
			
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
			$obj->nombre_usuario = $this->input->post("reg_nombre_usuario_txt");
            $obj->contrasena = $this->input->post("reg_contrasena_txt");
            $obj->email = $this->input->post("reg_email_txt");
			 // Datos personales (tabla persona)
			$obj->nombre = $this->input->post("reg_nombre_txt");
            $obj->apellido_paterno = $this->input->post("reg_apellido_pat_txt");
            $obj->apellido_materno = $this->input->post("reg_apellido_mat_txt");
			$obj->direccion = $direccion;
			$obj->pais = $this->input->post("pais_cmb");
			$obj->region = $this->input->post("region_cmb");
			$obj->provincia = $this->input->post("provincia_cmb");
			$obj->comuna = $this->input->post("comuna_cmb");
			$obj->telefono_particular = $telefono;
			$obj->telefono_celular = $celular;


			// Si está OK, valida que el usuario no exista (usuario único)
            if (!$this->usuario_modelo->existeNombreUsuario($obj->nombre_usuario)) {
                 
                // Ahora valida email (que sea único, no exista)
                if (!$this->usuario_modelo->existeEmail($obj->email)) {

                    // Inserta nuevo usuario
                    if($this->usuario_modelo->creaUsuario($obj)) {
                        $salida = array("exito" => "si",
    							        "mensaje" => "Usuario creado satisfactoriamente");
                    } else {
                        $salida = array("exito" => "no",
    							        "mensaje" => "No se pudo crear el usuario");
                    }
                } else {
                    $salida = array("exito" => "no",
							        "mensaje" => "El e-mail " . $obj->email . " ya está registrado");
                }

            } else {
                $salida = array("exito" => "no",
							    "mensaje" => "El nombre de usuario " . $obj->nombre_usuario . " ya existe");
            }

        } else {
			$salida = array("exito" => "no",
							"mensaje" => validation_errors());
		}
		
		echo json_encode($salida);
    }
	
	
	
} /* Fin Class Registro */