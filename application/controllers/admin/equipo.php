<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Equipo extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("admin/equipo_modelo", "", TRUE);
    }

	public function index() {
        // Llama por defecto a la función que trae todos los países
        $registros = $this->mantenedor("get");

        // Carga la vista en el layout (template)
        $data["contenido"] = "adm_equipo_vista";
        $data["contenidoExtra"] = $registros;
        $this->load->view("admin/layout", $data);
	}

    function mantenedor($accion, $valor=0) {
        $resultado = null;

        switch($accion) {
            case "get":
                $resultado = $this->equipo_modelo->traer();
                return $resultado;
                break;
            case "add":

                $this->form_validation->set_rules("nombre_pais_txt", "Nombre país", "trim|required");
                $this->form_validation->set_rules("sigla_txt", "Sigla", "trim|required|max_length[3]|min_length[3]");

                $this->form_validation->set_message("required", "El campo %s es requerido");
                $this->form_validation->set_message("min_length", "%s debe ser mínimo de %s caracteres");
                $this->form_validation->set_message("max_length", "%s debe ser máximo de %s caracteres");

                if($this->form_validation->run()) {
                    $objPais = new stdClass();
                    $objPais->nombre = strtoupper($this->input->post("nombre_pais_txt"));
                    $objPais->sigla = strtoupper($this->input->post("sigla_txt"));

                    if($this->pais_modelo->agregar($objPais)) {
                        $resultado = "<p>País agregado exitosamente</p>";
                    } else {
                        $resultado = "<p>No se pudo agregar el país</p>";
                    }
                } else {
                    $resultado = validation_errors();
                }
                echo utf8_decode($resultado.'<a href="'.base_url().'admin/pais">volver al mantenedor</a>');
                break;
            case "edit":
                $resultado = "<p>editar</p>";
                echo utf8_decode($resultado.'<a href="'.base_url().'admin/pais">volver al mantenedor</a>');
                break;
            case "update":
                $resultado = "<p>actualizar</p>";
                echo utf8_decode($resultado.'<a href="'.base_url().'admin/pais">volver al mantenedor</a>');
                break;
            default:
                echo "no hay acción definida";
                break;
        }
    }
}