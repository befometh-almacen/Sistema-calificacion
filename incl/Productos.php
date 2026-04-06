<?php
/**
 * @author Cristyan Fernando Morales Acevedo
 * Motor de Productos, conecta y gestiona todos los procesos vinculados al manejo de la tabla Productos
 */
namespace Clases;
use PDOException;

class Productos extends Conexion
{
    public function __construct(){
        parent::__construct();
    }

    /** Lista toda la tabla de productos por las columnas id, nombre, familia y pvp
     * @return array Lista de productos
     */
    public function listar() {
        $sql = "SELECT p.id, p.nombre, f.nombre AS familia, p.pvp
                    FROM productos p
                    LEFT JOIN familias f ON f.cod = p.familia";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }

    /** Busca los votos de un producto, tiene dos funciones distintas
     * @param $id int id del producto
     * @param $us int id del usuario que consulta, por defecto 0, (en ese caso entrega la media y el número de votos de dicho producto)
     * @return array|mixed para us==0: valor punto flotante y cantidad de votos, para us!=0: confirma si el usuario ya ha realizado voto
     */
    public function buscarVotos($id, $us=0){
        try{
            //1. Permite obtener el promedio y el total de cada producto
            if($us == 0){
                $stmt= $this->pdo->prepare("SELECT AVG(puntuacion) as valor, COUNT(*) as cantidad FROM votos WHERE id_producto = :i");
                $stmt->execute(array("i" => $id));
            //2. O permite verificar si el usuario en uso ya hizo su voto
            } else{
                $stmt= $this->pdo->prepare("SELECT 1 FROM votos WHERE id_producto = :i and id_usuario = :u");
                $stmt->execute(array("i" => $id, "u" => $us));
            }
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }

    /** Función que recibe la intención de votación del usuario.
     * @param $idProd int id del producto a votar
     * @param $puntos int valor de 1 a 5 del número de estrellas de dicho producto
     * @param $idUser int usuario que realiza la votación
     * @return false|string[]
     */
    public function votar($idProd, $puntos, $idUser) {
        try {
            // 1. Si el usuario ya votó, devolvemos false
            if($this->buscarVotos($idProd, $idUser)) {
                return false;
            }

            // 2. Si no ha votado, se realiza una inserción de voto
            $stmt = $this->pdo->prepare("INSERT INTO votos (id_producto, id_usuario, puntuacion) VALUES (?, ?, ?)");
            $resultado = $stmt->execute([$idProd, $idUser, $puntos]);

            // 3. Si se insertó bien, devolvemos un array (para que is_array() sea true)
            if ($resultado) {
                return ["status" => "ok"];
            }

            return false;

        } catch (PDOException $e) {
            error_log("Error en votar: " . $e->getMessage());
            return false;
        }
    }
}