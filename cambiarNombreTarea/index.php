<?php
//modificar el nombre de una tarea por id
$ok = isset($_POST['nombre'],$_POST['id']) ? true : false;
$return = new stdClass();
//si no llegan ambos, no se hara nada
if($ok){
    require('../helpers/input-filter.php');
    require('../controllers/tareas.php');
    $id = inputFilter($_POST['id']);
    $nombre = inputFilter($_POST['nombre']);
    $tarea = new Tareas();
    $return = $tarea->cambiarNombreTarea($id,$nombre);
}
else{
    $return->msg = "Parametros incompletos";
    $return->ok = "false";
    $return->status = 400;
}

// http_response_code($return->status);
// unset($return->status);
print(json_encode($return));


