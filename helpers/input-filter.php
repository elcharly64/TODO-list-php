<?php
//filtrado de datos de formularios para quitar posibles inyecciones
function inputFilter($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  