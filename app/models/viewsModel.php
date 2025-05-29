<?php

namespace app\models;

class viewsModel{

    /*---------- Modelo obtener vista ----------*/
    protected function obtenerVistasModelo($vista){
        # Array de vistas protegidas
        $listaBlanca = ["dashboard", "userNew", "userList", "userSearch", "userUpdate", "userPhoto", "logOut"];

        # vista protegida existe en array?
        if (in_array($vista, $listaBlanca)) {
            if (is_file("./app/views/content/" . $vista . "-view.php")) {
                $contenido = "./app/views/content/" . $vista . "-view.php";
            } else {
                $contenido = "404";
            }
        } elseif ($vista == "login" || $vista == "index" ) {
            $contenido = "login";
        } else {
            $contenido = "404";
        }

        return $contenido;
    }
}