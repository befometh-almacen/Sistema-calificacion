<?php
namespace Clases;
use PDO;
use PDOException;

class Conexion {
    private $host = "localhost";
    private $db   = "practicaUnidad7";
    private $user = "root"; // Ajusta según tu configuración de XAMPP/Linux
    private $pass = "";     // Ajusta según tu configuración
    private $charset = "utf8mb4";
    protected $pdo;

    public function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Reportar errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver arrays asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar preparación real de SQL
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
             error_log($e->getMessage()); // Enviar información al log de XAMPP
        }
    }
    public function conexion()
    {
        return $this->pdo;
    }
}
?>