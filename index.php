<?php
require_once "./config/app.php";
require_once "./autoload.php";

/*---------- Iniciando sesion ----------*/
require_once "./app/views/inc/session_start.php";

# Obtener URL
if (isset($_GET['views'])) {
    $url = explode("/", $_GET['views']);
} else {
    $url = ['login'];
}
?>

<!doctype html>
<html lang="es">

<head>
    <?php require_once "./app/views/inc/head.php" ?>
</head>

<body>
    <?php
    use app\controllers\viewsController;
    use app\controllers\loginController;

    # Instancia de la clase loginController
    $insLogin = new loginController();

    # Instancia de la clase viewsController
    $viewsController = new viewsController();
    $vista = $viewsController->obtenerVistasControlador($url[0]);

    if ($vista == "login" || $vista == "404") {
        require_once "./app/views/content/" . $vista . "-view.php";
    } else {
        # FILTRO SEGURIDAD, NO VER VISTA SIN INICIAR SESION
        if (!isset($_SESSION['id']) || !isset($_SESSION['nombre'])
        || !isset($_SESSION['usuario']) || $_SESSION['id'] == "" ||
        $_SESSION['nombre'] == "" || $_SESSION['usuario'] == "") {
            $insLogin->cerrarSesionControlador();
            exit();
        }
        require_once "./app/views/inc/navbar.php";
        require_once $vista;
    }

    /* <!-- JAVASCRIPT SCRIPTS --> */
    require_once "./app/views/inc/script.php"
    ?>
</body>

</html>