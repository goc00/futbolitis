<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Administrador extends CI_Controller {

	public function index()
	{
        // Carga la vista en el layout (template)
        $data["contenido"] = "adm_inicio_vista";
        $this->load->view("admin/layout", $data);
	}

    function ir($ir) {
        $data["contenido"] = $ir;
        $this->load->view("admin/layout", $data);
    }
}