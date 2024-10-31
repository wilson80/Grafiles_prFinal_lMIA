<?php

require_once "servicio/usuarios.servise.php";
require_once "servicio/folder.servise.php";


class AdminControlador
{
    private $servicioUsuarios;
    private $servicioCarpetas;
    private $usuarios; // Definimos la propiedad para los usuarios
    private $u;
    public function __construct()
    {
        $this->servicioUsuarios = new UsuariosService();
        $this->servicioCarpetas =  new FolderService();
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

    public function cambiarContrasenia()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];

        require_once "vista/areaAdmin/header.php";
        require_once "vista/cambioPassword/cambioPassword.php";
        require_once "vista/areaAdmin/foot.php";
    }

    public function CambiarPassword()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];

        if (isset($_POST['idU']) && $_POST['passwordant'] && $_POST['password2']) {
            $this->servicioUsuarios->cambiarContrasena($_POST['idU'], $_POST['passwordant'], $_POST['password2']);
            echo "<script>
                    window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=Inicio&n=" . urlencode($n) . "&rol=" . $rol . "&id=" . $id . "';
                    alert('Contraseña actualizada correctamente');
                </script>";
            exit();
        }
    }

    public function Carpetas()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idCarpeta = $_GET['idC'];
        $ruta = "home";
        if ($idCarpeta === '0x0') {
            $carpetas = $this->servicioCarpetas->obtenerCarpetaRaiz($id);
            require_once "vista/areaAdmin/header.php";
            require_once "vista/carpetas/carpetas.php";
            require_once "vista/areaAdmin/foot.php";

            //var_dump($carpetas);
        }
    }

    public function CarpetasSub()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idCarpeta = $_GET['idC'];
        $ruta = $_GET['ruta'];
        $carpetas = $this->servicioCarpetas->obtenerCarpetasDeCarpetas($id, $idCarpeta);
        require_once "vista/areaAdmin/header.php";
        require_once "vista/carpetas/carpetasSub.php";
        require_once "vista/areaAdmin/foot.php";

        //var_dump($carpetas);

    }

    public function GuardarCarpeta()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idCarpetaM = $_GET['idC'];
        $nameCarpeta = $_POST['folderName'];
        $this->servicioCarpetas->crearCarpetasEnCarpetas($nameCarpeta, $id, $idCarpetaM);
        // Aquí puedes agregar la lógica para guardar la carpeta, si no lo has hecho aún.

        // Redirigir usando header
        header("Location: http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpetaM}");
        exit();
    }

    public function RenombrarCarpeta()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idCarpeta = $_GET['idCM'];
        $rename = $_GET['rename'];
        $ruta = $_GET['ruta'];

        $data = [
            'nombre' => $rename,
            'ficheroMadre' => $idCarpeta
        ];
        $this->servicioCarpetas->actualizarNombreCarpeta($_GET['idC'], $data);
        echo "<script>
        history.replaceState(null, null, null);

        // Navega a la nueva URL
        window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpeta}&ruta={$ruta}';
        history.replaceState(null, null, null);
        </script>";
        exit();
    }

    public function MoverPapelera()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idCarpeta = $_GET['idCM'];
        $idCEliminar = $_GET['idC'];
        $ruta = $_GET['ruta'];
        $this->servicioCarpetas->eliminarCarpeta($idCEliminar);
        echo "<script>
        history.replaceState(null, null, null);

        // Navega a la nueva URL
        window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpeta}&ruta={$ruta}';
        history.replaceState(null, null, null);
        </script>";
        exit();
    }
}
