<?php

require_once "servicio/usuarios.servise.php";
require_once "servicio/folder.servise.php";
require_once "servicio/file.servise.php";


class AdminControlador
{
    private $servicioUsuarios;
    private $servicioCarpetas;
    private $usuarios; // Definimos la propiedad para los usuarios
    private $u;
    private $servicioArchivos;
    public function __construct()
    {
        $this->servicioUsuarios = new UsuariosService();
        $this->servicioCarpetas =  new FolderService();
        $this->servicioArchivos = new FilesService();
    }

    public function Inicio()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        //$this->llavedeAcceso($_GET['rol']);
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
        $archivos = $this->servicioArchivos->getFiles($idCarpeta, $id);
        $usuarios = $this->servicioUsuarios->obtenerUsuarios();
        if ($ruta === 'home/compartida') {
            $archivosC = $this->servicioArchivos->getSharedFiles($idCarpeta, $id);
            require_once "vista/areaAdmin/header.php";
            require_once "vista/carpetas/compartidos.php";
            require_once "vista/areaAdmin/foot.php";
        } else {
            require_once "vista/areaAdmin/header.php";
            require_once "vista/carpetas/carpetasSub.php";
            require_once "vista/areaAdmin/foot.php";
        }


        //var_dump($carpetas);

    }

    public function EliminarArchivosDelSistema()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $ruta = $_GET['ruta'];
        $idCarpetaM = $_GET['idC'];
        $idA = $_GET['idA'];
        $this->servicioArchivos->deleteFilePermanently($idA);
        if ($ruta === 'papelera') {
            header("Location: http://localhost/grafiles_mia/?c=admin&a=Papelera&n={$n}&rol={$rol}&id={$id}");
            exit();
        } else {
            header("Location: http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpetaM}&ruta={$ruta}");
            exit();
        }
    }

    public function GuardarCarpeta()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $ruta = $_GET['ruta'];
        $idCarpetaM = $_GET['idC'];
        $nameCarpeta = $_POST['folderName'];
        $this->servicioCarpetas->crearCarpetasEnCarpetas($nameCarpeta, $id, $idCarpetaM);
        // Aquí puedes agregar la lógica para guardar la carpeta, si no lo has hecho aún.

        // Redirigir usando header
        header("Location: http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpetaM}&ruta={$ruta}");
        exit();
    }

    public function GuardarArchivo()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $ruta = $_GET['ruta'];
        $idCarpetaM = $_GET['idC'];

        $data = [
            'extension' => $_POST['fileExtension'],
            'nombre' => $_POST['fileName'],
            'contenido' => $_POST['fileContent'],
            'idFM' => $idCarpetaM,
            'idU' => $id
        ];
        $this->servicioArchivos->addFile($data);
        header("Location: http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpetaM}&ruta={$ruta}");
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

    public function EditarArchivo()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $ruta = $_GET['ruta'];
        $idCarpetaM = $_GET['idC'];

        $data = [
            'extension' => $_POST['editFileExtension'],
            'nombre' => $_POST['editFileName'],
            'contenido' => $_POST['editFileContent'],
            'idFM' => $idCarpetaM,
            'idArchivo' =>  $_POST['idEditFile']
        ];
        $r = $this->servicioArchivos->editFile($data);

        header("Location: http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpetaM}&ruta={$ruta}");
        exit();
    }

    public function MoverPapeleraArchivo()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idCarpeta = $_GET['idCM'];
        $idAEliminar = $_GET['idA'];
        $ruta = $_GET['ruta'];
        $this->servicioArchivos->moveFileToTrash($idAEliminar);
        echo "<script>
        history.replaceState(null, null, null);

        // Navega a la nueva URL
        window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpeta}&ruta={$ruta}';
        history.replaceState(null, null, null);
        </script>";
        exit();
    }

    public function CompartirArchivo()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idCarpeta = $_GET['idCM'];
        $idA = $_GET['idA'];
        $idU = $_GET['idU'];
        $ruta = $_GET['ruta'];
        $idFM = 'null';
        $c = $this->servicioCarpetas->obtenerCarpetaCompartida($idU);
        foreach ($c as $carpeta) {
            $idFM = $carpeta; // Reemplaza 'propiedad' por el nombre de la propiedad que deseas obtener
            break; // Esto detiene el bucle después del primer elemento
        }

        $data = [
            'idFM' => $idFM,
            'idU' => $idU,
            'idUC' => $n,
            'idA' => $idA
        ];
        $this->servicioArchivos->shareFile(($data));

        echo "<script>
        alert('Archivo compartido');

        // Navega a la nueva URL
        window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n={$n}&rol={$rol}&id={$id}&idC={$idCarpeta}&ruta={$ruta}';
        history.replaceState(null, null, null);
        </script>";
        exit();
    }

    public function Papelera()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $this->llavedeAcceso(($rol));
        $carpetas = $this->servicioCarpetas->obtenerCarpetasEliminadas();
        $archivos = $this->servicioArchivos->getDeletedFiles('00');
        require_once "vista/areaAdmin/header.php";
        require_once "vista/carpetas/papelera.php";
        require_once "vista/areaAdmin/foot.php";
    }

    public function EliminarCarpetaSistema()
    {
        $n = $_GET['n'];
        $rol = $_GET['rol'];
        $id = $_GET['id'];
        $idC = $_GET['idC'];
        $this->servicioCarpetas->eliminarCarpetaDelSistema($idC);
        header("Location: http://localhost/grafiles_mia/?c=admin&a=Papelera&n={$n}&rol={$rol}&id={$id}");
        exit();
    }
}
