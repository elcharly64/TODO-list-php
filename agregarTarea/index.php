<?php
//agregar una nueva tarea, nombre requerido
require('../helpers/input-filter.php');
$return = new stdClass();
if(isset($_POST['nombre'])){
    require_once("../controllers/tareas.php");
    $tarea = new Tareas();
    $nombre = inputFilter($_POST['nombre']);
    $return = $tarea->createTarea($nombre);
}
else{
    $return->msg = "El nombre es obligatorio";
    $return->ok = "false";
    $return->status = 400;
}

// http_response_code($return->status);
// unset($return->status);
print(json_encode($return));





