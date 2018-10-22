<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Futbolitis - ADMINISTRADOR</title>

</head>

<body>

    <div style="float:left; margin:10px;">
    <?php
        $this->load->view("admin/adm_menu_vista");
    ?>
    </div>
    <div>
    <?php
        if(isset($contenidoExtra)) {
            $info["contenidoExtra"] = $contenidoExtra;
            $this->load->view("admin/".$contenido, $info);
        } else {
            $this->load->view("admin/".$contenido);
        }
    ?>
    </div>

</body>
</html>