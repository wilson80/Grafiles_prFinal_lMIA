<?php

require_once "servicio/login.servise.php";
// define('API_URL', 'http://localhost:3500/connect');
define('API_URL', 'http://python_backend:3500/connect');

class LoginControlador
{
    private $servicioLogin;

    public function __construct()
    {
        $this->servicioLogin = new LoginService();
    }

    public function Inicio()
    {
        require_once "vista/login/login.php";
    }

    public function Loguearse()
    {
        if (isset($_POST['userId'], $_POST['password'])) {
            $username = $_POST['userId'];
            $password = $_POST['password'];
            //echo "Datos recibidos: Usuario - $username, Contraseña - $password"; // Depuración

            // Llama a la API con el usuario y la contraseña
            $u = $this->servicioLogin->login($username, $password);

            if ($u === null) {
                $this->mostrarAlertaNoExisteUsuario();
            } else {
                // Convierte `$u` en JSON para usarlo como string en JavaScript
                $usuarioJson = json_encode($u);

                // Guardar en localStorage como JSON.stringify
                echo "<script>
                    localStorage.setItem('usuario', JSON.stringify($usuarioJson));
                    console.log('Usuario guardado en localStorage:', ($usuarioJson));
                    
                    window.location.href = 'controlador/dashboard.php'; // Asegúrate que la ruta sea correcta
                    
                </script>";
                exit();
            }
        } else {
            $this->Inicio(); // Redirige al formulario de inicio de sesión si faltan datos
        }
    }


    public function LogOut()
    {
        echo "<script>
            if (confirm('¿Está seguro que desea cerrar sesión?')) {
                localStorage.removeItem('usuario');
                console.log('Usuario eliminado de localStorage');
                history.replaceState(null, null, window.location.href);
                window.location.href = '?c=login';
            } else {
                window.history.back();
            }
        </script>";
        exit();
    }

    private function mostrarAlertaNoExisteUsuario(): void
    {
        echo "<script>
            alert('No existe el usuario.');
            window.location.href = '?c=login';
        </script>";
        exit();
    }
}
