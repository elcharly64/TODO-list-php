<?php
//modificar el estado de una tarea por id
$ok = isset($_POST['id'],$_POST['completadoEn']) ? true : false;
$return = new stdClass();
//si no llegan ambos, no se hara nada
if($ok){
    //si el completadoEn tiene 1, se pone la tarea completada en el time actual
    //la pongo pendiente en otro caso
    $completadoEn = $_POST['completadoEn'];
    $id = $_POST['id'];
    //completo la tarea colocandole la fecha de finalizacion
    //o la pondgo pendiente colocandole string vacio
    require('../controllers/tareas.php');
    $tarea = new Tareas();
    $return = $tarea->completarTarea($id,$completadoEn);
}
else{
    $return->msg = "Faltan parametros requeridos";
    $return->ok = "false";
    $return->status = 400;
}

// http_response_code($return->status);
// unset($return->status);
print(json_encode($return));


