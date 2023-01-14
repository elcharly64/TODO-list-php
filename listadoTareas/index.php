<?php

/* 
endpoint que retorna el listado con paginacion de tareas por condicion de su estado
todos los parametros son opcionales: el estado de las tareas (0=todas, 1=completadas, 2=pendientes),
el inicio (desde) y la cantidad (limite) y tienen default a 0, 0 y 5 respectivamente
 */
$estado = isset($_POST['estado']) ? $_POST['estado'] : 0;
$desde = isset($_POST['desde']) ? $_POST['desde'] : 0;
$limite = isset($_POST['limite']) ? $_POST['limite'] : 5;
require_once("../controllers/tareas.php");
$tarea = new Tareas();
$return = $tarea->listadoTareas($desde,$limite,$estado);
print_r($return);



