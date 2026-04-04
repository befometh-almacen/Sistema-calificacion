<?php
namespace Clases;
use PDOException;

class Productos extends Conexion
{
    public function __construct(){
        parent::__construct();
    }

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

    public function buscarVotos($id, $us = 0){
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

    public function votar($idProd, $puntos, $idUser = 1) {
        try {
            // 1. Si ya existe el voto, devolvemos false
            if($this->buscarVotos($idProd, $idUser)) {
                return false;
            }

            // 2. Preparamos e insertamos
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