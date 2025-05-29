<?php
	namespace app\controllers;
	use app\models\mainModel;

	class loginController extends mainModel {
        # Controlador Iniciar Sesion
        public function iniciarSesionControlador(){
            # Almacenando datos#
		    $usuario = $this->limpiarCadena($_POST['login_usuario']);
		    $clave = $this->limpiarCadena($_POST['login_clave']);

            # Verificando campos obligatorios #
		    if($usuario=="" || $clave==""){
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Rellenar Campos Obligatorios',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>
                ";
        } else {
            # Verificando integridad de los datos (usuario, clave) #
            if (($this->verificarDatos("[a-zA-Z0-9]{4,20}",$usuario))) {
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Formato de Usuario Erroneo',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>
                ";
            } else {
                if (($this->verificarDatos("[a-zA-Z0-9@\$.\-]{7,100}",$clave))) {
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Formato de Clave Erroneo',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>
                ";
            } else {
                # Verificando usuario NO este repetido #
                $check_usuario = $this->ejecutarConsulta("SELECT * FROM
                usuario WHERE usuario_usuario='$usuario'");

                if ($check_usuario->rowCount() == 1) {
                    $check_usuario = $check_usuario->fetch();
                    # si usuario y clave coinciden
                    if ($check_usuario['usuario_usuario'] == $usuario && password_verify($clave, $check_usuario['usuario_clave'])) {
                        # VARIABLES DE SESION DEL USUARIO
                        $_SESSION['id'] = $check_usuario['usuario_id'];
                        $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                        $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                        $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
                        $_SESSION['foto'] = $check_usuario['usuario_foto'];

                        # Redireccionar usuario al dashboard
                        if (headers_sent()) {
                            echo "
                            <script>
                                window.location.href = '".APP_URL."dashboard/';                               </script>
                            </script>";
                        } else {
                            header("Location: " . APP_URL . "dashboard/");
                        }


                    } else {
                     echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Usuario o Clave Incorrectos',
                                confirmButtonText: 'Aceptar'
                            });
                        </script>
                ";
                    }

                } else {
                     echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Usuario o Clave Incorrectos',
                                confirmButtonText: 'Aceptar'
                            });
                        </script>
                ";
                }


                }

            }

        }

        }

        # Controlador Cerrar Sesion
        public function cerrarSesionControlador(){
            session_destroy();

            if (headers_sent()) {
                echo "
                <script>
                    window.location.href = '".APP_URL."login/';                               </script>
                </script>";
            } else {
                header("Location: " . APP_URL . "login/");
            }
        }
    }
