<?php
class Tareas{
    public string $id;
    public string $nombre;
    public string $completadoEn;

    function __construct(){
        require_once("../helpers/randomString.php");
        $this->id = random_str(16);
    }

    private function set($nombre="",$completadoEn=""){
        $this->nombre = $nombre;
        $this->completadoEn = $completadoEn;
    }

    
    function TareasArray(){
        //arreglo de todas las tareas guardadas en archivo
        $tareasArray = array();
        if(file_exists("../DB/data.json")){
            $losElementos = file_get_contents("../DB/data.json");
            $tareasArray = json_decode($losElementos);
        }
        return $tareasArray;
    }
    
    function TareaObjeto(){
        $tareaObjeto = new stdClass();
        $tareaObjeto->id = $this->id;
        $tareaObjeto->nombre = $this->nombre;
        $tareaObjeto->completadoEn = $this->completadoEn;
        return $tareaObjeto;
    }
    
    function guardarTareas($lasTareas){
        $archivo = fopen("../DB/data.json","w");
        fwrite($archivo,$lasTareas);
        //        echo "guardando ".count($lasTareas).PHP_EOL;
        fclose($archivo);
    }
    
    function actualizarTareas(){
        //agrego la tarea actual al arreglo
        $tareasArray = $this->TareasArray();
        $tareasArray[] = $this->TareaObjeto();
        return $tareasArray;
    }
    
    function createTarea($nombre=""){
        //agrego la tarea al arreglo y la guardo en archivo
        $return = new stdClass();
        if($nombre == ""){
            $return->ok = "false";
            $return->msg = "El nombre no puede estar vacío";
            $return->status = 400;
            return $return;
        }
        
        $this->set($nombre,"");
        $tareasArray = $this->actualizarTareas();
        $lasTareas = json_encode($tareasArray);
        $this->guardarTareas($lasTareas);
        $return->ok = "true";
        $return->msg = "Tarea creada con exito";
        $return->status = 200;
        $return->tarea = $this->TareaObjeto();
        return $return;
    }
    
    function completarTarea($id,$accion = 1){
        //marca completa (accion = 1) o pendiente (accion = 0)
        //retorno la tarea completada/pendiente
        $return = new stdClass();
        $acciones = array(0,1);
        $valores = array("",date("d-m-Y H:i:s",time()-5*3600));
        $indice = array_search($accion,$acciones);
        if($indice === false){
            $return->ok = "false";
            $return->msg = "Solo se permite dejar tareas pendientes o completadas";
            $return->status = 400;
            return $return;
        }
        $valor = $valores[$indice];
        $tareasArray = $this->TareasArray();
        if(count($tareasArray)!==0){
            //busco la tarea por id
            $indice = $this->buscarTareaPorId($id);
            if($indice === false){
                $return->ok = "false";
                $return->msg = "Tarea no encontrada";
                $return->status = 200;
                return $return;
            }
            $objeto = $tareasArray[$indice];
            $objeto->completadoEn = $valor;
            $tareasArray[$indice] = $objeto;
            $this->guardarTareas(json_encode($tareasArray));
            $return->ok = "true";
            $return->msg = "Estado de la tarea cambiado";
            $return->status = 200;
            $return->tarea = $objeto;
        }
        else{
            $return->ok = "false";
            $return->msg = "No hay tareas guardadas";
            $return->status = 200;
            return $return;
        }
        return $return;
    }

    function cambiarNombreTarea($id,$nombre){
        //cambiar nombre de la tarea id
        $return = new stdClass();
        if($nombre == ""){
            $return->ok = "false";
            $return->msg = "El nombre no puede estar en blanco";
            $return->status = 400;
            return $return;
        }
        $tareasArray = $this->TareasArray();
        if(count($tareasArray)!==0){
            //busco la tarea por id
            $indice = $this->buscarTareaPorId($id);
            if($indice === false){
                $return->ok = "false";
                $return->msg = "Tarea no encontrada";
                $return->status = 200;
                return $return;
            }
            $objeto = $tareasArray[$indice];
            $objeto->nombre =$nombre;
            $tareasArray[$indice] = $objeto;
            $this->guardarTareas(json_encode($tareasArray));
            $return->status = 200;
            $return->ok = "true";
            $return->msg = "Nombre cambiado correctamente";
            $return->tarea = $objeto;
        }
        else{
            $return->ok = "false";
            $return->msg = "No hay tareas guardadas";
            $return->status = 200;
            return $return;
        }
        return $return;
    }

    function BorrarTarea($id){
        //borrar tarea id, retorna la tarea borrada
        $return = new stdClass();
        //busco la tarea por id
        $tareasArray = $this->TareasArray();
        if(count($tareasArray)!==0){
            $indice = $this->buscarTareaPorId($id);
            if($indice === false){
                $return->ok = "false";
                $return->msg = "Tarea no encontrada";
                $return->status = 200;
                return $return;
            }

            $objeto = $tareasArray[$indice];
            unset($tareasArray[$indice]);
            $this->guardarTareas(json_encode($tareasArray));
            $return->status = 200;
            $return->ok = "true";
            $return->msg = "Tarea eliminada correctamente";
            $return->tarea = $objeto;
        }
        else{
            $return->ok = "false";
            $return->msg = "No hay tareas guardadas";
            $return->status = 200;
        }
        return $return;
    }

    function buscarTareaPorId($id){
        $tareasArray = $this->TareasArray();
        $indice = -1;
        if(count($tareasArray)!==0){
            foreach ($tareasArray as $i => $objeto) {
                if($objeto->id === $id){
                    $indice = $i;
                    break;
                }
            }
        }
        unset($objeto);
        return $indice >= 0 ? $indice : false;
    }

    function listadoTareas($desde = 0, $limite = 5, $estado = 0){
        //retorna un listado paginado de tareas
        //estados, 0 = todas, 1 = completadas, 2 = pendientes, x = error
        //validacion de los posibles estados
        $acciones = array(0,1,2);
        $indice = array_search($estado,$acciones);
        $return = new stdClass();
        if($indice === false){
            $return->ok = "false";
            $return->msg = "Se debe proporcinar la opcion correcta para filtrar las tareas";
            $return->status = 400;
            return $return;
        }
        $tareasArray = $this->TareasArray();
        $tareasSalida = array();
        $numeroDeTareas = 0;//las que cumplen con la condición
        $totalDeTareas = 0;//total de tareas registradas
        foreach ($tareasArray as $indice => $objeto) {
            $totalDeTareas++;
            if($estado == 1){
                if($objeto->completadoEn !== "") 
                {
                    $tareasSalida[] = $objeto;
                    $numeroDeTareas++;
                }
            }
            else if($estado == 2){
                if($objeto->completadoEn === "") {
                    $tareasSalida[] = $objeto;
                    $numeroDeTareas++;
                }
            }
            else{
                $tareasSalida[] = $objeto;
                $numeroDeTareas++;
            }
        }
        $tareasSalida = array_slice($tareasSalida,$desde,$limite);
        $return->ok = "true";
        $return->status = 200;
        $return->msg = "Listado de tareas correcto";
        $return->tareas = $tareasSalida;
        $return->numero = count($tareasSalida);
        $return->total = count($tareasArray);
        return $return;
    }
    
    function buscarPorNombre($nombre){
        $return = new stdClass();
        //se valida que el nombre no est[e vacio
        if($nombre == ""){
            $return->ok = "false";
            $return->msg = "El texto de busqueda no puede estar vacio";
            $return->status = 400;
            return $return;
        }

        $tareasArray = $this->TareasArray();
        $tareasSalida = array();
        foreach ($tareasArray as $indice => $objeto){
            //hago la busqueda insensible a mayusculas
            if(stripos($objeto->nombre,$nombre) !== false){
                $tareasSalida[] = $objeto;
            }
        }

        $return->ok = "true";
        $return->msg = "Busqueda realizada correctamente";
        $return->status = 200;
        $return->tareas = $tareasSalida;
        return $return;
    }
}