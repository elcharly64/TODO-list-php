<?php

/* 
endpoint que elimina la tarea por id (obligatorio)
se retorna la tarea aliminada
 */
$return = new stdClass();
if(isset($_POST['id'])){
$id = $_POST['id'];
    require_once("../controllers/tareas.php");
    $tarea = new Tareas();
    $return = $tarea->BorrarTarea($id);
}
else{
    $return->ok = "false";
    $return->msg = "El id es obligatorio";
    $return->status = 400;
}

print_r($return);
