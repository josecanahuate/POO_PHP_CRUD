<?php
namespace app\models;

use PDO;
use PDOException;

if (file_exists(__DIR__  . "/../../config/server.php")) {
    require_once __DIR__  . "/../../config/server.php";
}

class mainModel {
    private $server = DB_SERVER;
    private $db = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;

    #Conexion a BD
    protected function conectar(){
        try {
            $conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db, $this->user, $this->pass);
            $conexion->exec("SET CHARACTER SET utf8");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }


    protected function ejecutarConsulta($consulta){
        #conexion a la bd y preparar consulta
        $sql = $this->conectar()->prepare($consulta);
        $sql->execute();
        return $sql;
    }

    # Funcion para Evitar Inyeccion SQL
 	public function limpiarCadena($cadena){

        # CONSULTAS QUE NO SE PERMITIRAN
        $palabras = ["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," SELECT ","DELETE FROM",
        "INSERT INTO","DROP TABLE","DROP DATABASE","TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","
        <",">","==","=",";","::"];

    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);

    foreach($palabras as $palabra){
    $cadena = str_ireplace($palabra, "", $cadena);
    }

    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);

    return $cadena;
    }


    #Filtro para evitar inyecciones y comparar con los formatos solicitados
    protected function verificarDatos($filtro, $cadena){
    if (preg_match("/^".$filtro."$/", $cadena)) {
        return false;
    } else {
        return true;
    }
    }


    # CRUD - GUARDAR DATOS
 protected function guardarDatos($tabla, $datos){
    $columnas = [];
    $marcadores = [];
    $valores = [];

    foreach ($datos as $clave) {
        $columnas[] = $clave["campo_nombre"];
        $marcadores[] = $clave["campo_marcador"];
        $valores[$clave["campo_marcador"]] = $clave["campo_valor"];
    }

    $query = "INSERT INTO $tabla (" . implode(",", $columnas) . ") VALUES (" . implode(",", $marcadores) . ")";

    $sql = $this->conectar()->prepare($query);

    foreach ($valores as $marcador => $valor) {
        $sql->bindValue($marcador, $valor); // puedes usar bindParam también si lo prefieres
    }

    $sql->execute();
    return $sql;
}


    # CRUD - SELECCIONAR DATOS
    public function seleccionarDatos($tipo, $tabla, $campo, $id){
        #Limpiar Parametros
        $tipo = $this->limpiarCadena($tipo);
        $tabla = $this->limpiarCadena($tabla);
        $campo = $this->limpiarCadena($campo);
        $id = $this->limpiarCadena($id);

        # Consultas a tabla || Consultas a campos
        if ($tipo == "Unico") {
            $sql = $this->conectar()->prepare("SELECT * FROM $tabla
            WHERE $campo=:ID");
            $sql->bindParam(":ID", $id);

        } elseif ($tipo == "Normal") {
            $sql = $this->conectar()->prepare("SELECT $campo FROM $tabla");
        }

        $sql->execute();
        return $sql;
    }


    # CRUD - ACTUALIZAR DATOS
    protected function actualizarDatos($tabla, $datos, $condicion){
        $query = "UPDATE $tabla SET ";
        $C=0;
        foreach ($datos as $clave) {
            if ($C >= 1) {
            $query.=",";
            }
            $query.=$clave["campo_nombre"] . "=" . $clave["campo_marcador"];
            $C++;
        }
        $query.=" WHERE " . $condicion["condicion_campo"] . "=" . $condicion["condicion_marcador"];

        $sql = $this->conectar()->prepare($query);

        foreach ($datos as $clave) {
            $sql->bindParam($clave["campo_marcador"], $clave["campo_valor"]);
        }

        $sql->bindParam($condicion["condicion_marcador"], $condicion["condicion_valor"]);
        $sql->execute();
        return $sql;
    }


    # CRUD - ELIMINAR DATOS
    protected function eliminarRegistro($tabla, $campo, $id){
        $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
        $sql->bindParam(":id", $id);
        $sql->execute();
        return $sql;
    }


    # PAGINACION TABLA
   	protected function paginadorTablas($pagina,$numeroPaginas,$url,$botones){
        $tabla='<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

        if($pagina<=1){
            $tabla.='
            <a class="pagination-previous is-disabled" disabled >Anterior</a>
            <ul class="pagination-list">
            ';
        }else{
            $tabla.='
            <a class="pagination-previous" href="'.$url.($pagina-1).'/">Anterior</a>
            <ul class="pagination-list">
                <li><a class="pagination-link" href="'.$url.'1/">1</a></li>
                <li><span class="pagination-ellipsis">&hellip;</span></li>
            ';
        }

        $ci=0;
        for($i=$pagina; $i<=$numeroPaginas; $i++){

            if($ci>=$botones){
                break;
            }

            if($pagina==$i){
                $tabla.='<li><a class="pagination-link is-current" href="'.$url.$i.'/">'.$i.'</a></li>';
            }else{
                $tabla.='<li><a class="pagination-link" href="'.$url.$i.'/">'.$i.'</a></li>';
            }

            $ci++;
        }


        if($pagina==$numeroPaginas){
            $tabla.='
            </ul>
            <a class="pagination-next is-disabled" disabled >Siguiente</a>
            ';
        }else{
            $tabla.='
                <li><span class="pagination-ellipsis">&hellip;</span></li>
                <li><a class="pagination-link" href="'.$url.$numeroPaginas.'/">'.$numeroPaginas.'</a></li>
            </ul>
            <a class="pagination-next" href="'.$url.($pagina+1).'/">Siguiente</a>
            ';
        }

        $tabla.='</nav>';
        return $tabla;
	}


    }