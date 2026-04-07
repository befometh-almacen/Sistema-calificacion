<?php

namespace Clases;

class Usuarios extends Conexion
{
    public function __construct()
    {
        parent::__construct();
    }

    /**Función con dos funcionalidades diferentes:
     * @param $dato string el usuario o el id dependiendo la opción
     * @param $op boolean true: búsqueda por columna "id", false (por defecto): búsqueda por columna de nombre "usuario"
     * @return array|mixed id o nombre de usuario dependiendo el requerimiento
     */
    public function buscarUsuario($dato, $op=false){
        $opcion = $op?"id":"usuario";
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE $opcion = :dato");
        $stmt->execute(["dato"=>$dato]);
        return $stmt->fetch();
    }

    /** Recibe un usuario y contraseña y confirma si conrresponde con los registrados en base de datos, (recordar que se basa en sistema
     *  password_hash("pass",PASSWORD_DEFAULT))
     * @param $user string usuario de consulta
     * @param $pass string contraseña de consulta
     * @return false[] devuelve un Array [status = true/false si es correcta o incorrecta la contraseña, id (solo si existe el usuario y la contraseña es correcta)]
     */
    private function comprobarPass($user, $pass){
        $usuario = $this->buscarUsuario($user);
        $respuesta = ["status"=>false];
        //print_r(password_hash($pass, PASSWORD_DEFAULT)."//////".$usuario["password"]); //Debug
        if(!empty($usuario)){
            $respuesta["status"] = password_verify($pass, $usuario["password"]);
            $respuesta["id"] = $usuario["id"];
        }
        return $respuesta;
    }

    /**Recibe la llamada de comprobación de logueo y llama a las funciones que se encargan de realizar dicha confirmación
     * @param $user
     * @param $pass
     * @return false
     */
    public function logIn($user, $pass){
        $usuario = $this->comprobarPass($user, $pass); //devuelve un array(status->id(sólo si el status es true, de lo contrario no está este campo))
        if($usuario["status"])
            return $usuario["id"]; //Entrega el id de usuario al front
        else {
            return false; //Si la contraseña es incorrecta o el usuario no existe.
        }
    }
}