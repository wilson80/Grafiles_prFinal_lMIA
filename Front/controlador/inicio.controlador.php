<?php



class InicioControlador
{

    private $log;


    public function __construct()
    {
        // $this->log = new Login();
    }
    public function inicio()
    {
        //$db=basedatos::conectar();
        require_once "vista/login/login.php";
    }
}
