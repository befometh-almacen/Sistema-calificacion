<?php

namespace Clases;

use Clases\Conexion;

class Usuarios extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $dato string el usuario o el id dependiendo la opción
     * @param $op boolean true: búsqueda por columna id, false (por defecto): búsqueda por columna usuario
     * @return array|mixed usuario o usuarios dependiendo la solicitud
     */
    public function buscarUsuario($dato, $op=false){
        $opcion = $op?"id":"usuario";
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE $opcion = :dato");
        $stmt->execute(["dato"=>$dato]);
        return $stmt->fetch();
    }

    private function comprobarPass($user, $pass){
        $usuario = $this->buscarUsuario($user);
        $respuesta = ["status"=>false];
        print_r(password_hash($pass, PASSWORD_DEFAULT)."//////".$usuario["password"]);
        if(!empty($usuario)){
            $respuesta["status"] = password_verify($pass, $usuario["password"]);
            $respuesta["id"] = $usuario["id"];
        }
        return $respuesta;
    }

    public function logIn($user, $pass){
        $usuario = $this->comprobarPass($user, $pass);
        if($usuario["status"])
            return $usuario["id"];
        else {
            return false;
        }
    }
}