<?php

namespace app\controllers;
use app\models\viewsModel;

/*---------- Controlador obtener vistas ----------*/
class viewsController extends viewsModel{

  # Obteniendo parametro $vista desde el modelo, obtenerVistasModelo
  public function obtenerVistasControlador($vista){
    if ($vista!="") {
        $respuesta = $this->obtenerVistasModelo($vista);
    } else {
        $respuesta = "login";
    }

    return $respuesta;
  }
}