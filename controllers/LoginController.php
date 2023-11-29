<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router) {

        $alertas = [];
        $auth = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if( empty($alertas) ) {
                //Comporbar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);
                if($usuario) {
                    //verificar password
                    $resultado = $usuario->compPasswordAndConfirmado($auth->password);
                    if($resultado) {
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionamiento
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? NULL;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }

                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas,
            'auth' => $auth
        ]);
    }

    public static function logout() {
        session_start();

        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router) {
        
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if( empty($alertas) ) {
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === '1') {
                    //generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //TODO: Enviar el email
                    $email = new Email($usuario->nombre, $usuario->enail, $usuario->token);
                    $email->enviarIntrucciones();

                    //Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email, luego sigue las intrucciones');

                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }

        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvidePassword', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router) {
        
        $alertas = [];
        $error = false;
        $token = sanitizar($_GET['token']);

        //Buscar usuario por su token
        $usuario = Usuario::where('token', $token);
        if( empty($usuario) ) {
            Usuario::setAlerta('error', 'Token no valido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Leer el nuevo password y guardarlo
            $nuevoPassword = new Usuario($_POST);
            $alertas = $nuevoPassword->validarPassword();

            if( empty($alertas) ) {
                $usuario->password = NULL;
                $usuario->password = $nuevoPassword->password;

                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperarPassword', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function confirmar(Router $router) {
        
        $alertas = [];
        $token = sanitizar($_GET['token']);

        $usuario = Usuario::where('token', $token);
        if (empty ($usuario) ) {
            //Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            //Modificar a usuario confirmado = 1 
            $usuario->confirmado = 1;
            $usuario->token = NULL;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Confirmada Correctamente :)');
        }
        
        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmarCuenta', [
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje', []);
    }

    public static function crearCuenta(Router $router) {

        $usuario = new Usuario($_POST);

        //Alertas vacias
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            //Revisar que alertas este vacio
            if ( empty($alertas) ) {
                //Verificar que el usuario no este registraado
                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                }else {
                    //No esta registrado, hashear password
                    $usuario->hashPassword();
                    //Generar crear Token
                    $usuario->crearToken();
                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    //Crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        header('Location: /mensaje');
                    }
                }

            }
            
            // $errores = Usuario::getErrores();
        }
        
        $router->render('auth/crearCuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
}