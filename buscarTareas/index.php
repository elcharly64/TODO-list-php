<?php
//buscar por nombre de tarea (no sensible a mayusculas)
require('../helpers/input-filter.php');
$return = new stdClass();
if(isset($_POST['nombre'])){
    require_once("../controllers/tareas.php");
    $tarea = new Tareas();
    $nombre = inputFilter($_POST['nombre']);
    $return = $tarea->buscarPorNombre($nombre);
}
else{
    $return->msg = "El texto de busqueda es obligatorio";
    $return->ok = "false";
    $return->status = 400;
}

print_r($return);

