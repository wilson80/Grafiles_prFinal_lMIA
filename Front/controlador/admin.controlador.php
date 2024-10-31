<?php

require_once "servicio/usuarios.servise.php";
define('API_URL', 'http://localhost:3500/usuarios');


class AdminControlador
{
    private $servicioUsuarios;
    private $usuarios; // Definimos la propiedad para los usuarios
    private $u;
    public function __construct()
    {
        $this->servicioUsuarios = new UsuariosService();
    }

    public function Inicio()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $this->llavedeAcceso($_GET['rol']);
        require_once "vista/areaAdmin/header.php";
        require_once "vista/areaAdmin/foot.php";
    }

    public function llavedeAcceso(string $rol)
    {
        if ($rol !== 'administrador') {
            header('Location: controlador/dashboard.php');
            exit();
        }
    }

    public function Usuarios()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $this->llavedeAcceso($rol);

        // Llama a la API para obtener los usuarios
        $usuarios = $this->servicioUsuarios->obtenerUsuarios();

        // Verifica si hubo un error en la llamada a la API
        // $response = $this->servicioUsuarios->callAPI('GET', API_URL);
        //var_dump($usuarios); // Muestra la respuesta
        $this->usuarios = $usuarios; // Almacena los usuarios en la propiedad

        require_once "vista/areaAdmin/header.php";
        require_once "vista/areaAdmin/usuarios/todos.php";
        require_once "vista/areaAdmin/foot.php";
    }

    public function NuevoUsuario()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $titulo = "Ingrese los datos para el nuevo usuario";
        $accion = "Guardar usuario";
        $this->llavedeAcceso($_GET['rol']);

        $idU = '0';
        if (isset($_GET['idU'])) {
            $titulo = "Ingrese los datos para modificar el usuario";
            $accion = "Actualizar usuario";
            $u =  $this->servicioUsuarios->obtenerUsuarioPorId($_GET['idU']);
            $idU = $u['_id'];
        }
        require_once "vista/areaAdmin/header.php";
        require_once "vista/areaAdmin/usuarios/nuevo.php";
        require_once "vista/areaAdmin/foot.php";
    }

    public function GuardarUsuario()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $data = [
            'username' => $_POST['username'],
            'nombre' => $_POST['nombre'],
            'password' => $_POST['password2'],
            'rol' => $_POST['rol']
        ];
        $this->llavedeAcceso($_GET['rol']);
        if (isset($_POST['idU']) && $_POST['idU'] !== '') {
            $id = $_POST['idU'];

            $rs = $this->servicioUsuarios->actualizarUsuario($id, $data);

            echo "<script>
                    alert('Usuario actualizado correctamente');
                window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=Usuarios&n=" . urlencode($n) . "&rol=" . $rol . "&id=" . $id . "';
                </script>";
            exit();
        } else {
            $rs = $this->servicioUsuarios->guardarUsuario($data);

            echo "<script>
                    alert('Usuario Guardado correctamente');
                window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=Usuarios&n=" . urlencode($n) . "&rol=" . $rol . "&id=" . $id . "';
                </script>";
            exit();
        }
    }
}
