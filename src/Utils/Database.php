<?php
/**
 * Clase para manejar conexión a base de datos
 */

namespace Src\Utils;

class Database
{
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct()
    {
        $this->config = require __DIR__ . '/../../config/database.php';
        $this->connect();
    }

    /**
     * Obtener instancia singleton de la BD
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Conectar a la base de datos
     */
    private function connect()
    {
        try {
            $dsn = "mysql:host=" . $this->config['db_host'] . 
                   ";port=" . $this->config['db_port'] . 
                   ";dbname=" . $this->config['db_name'] . 
                   ";charset=" . $this->config['db_charset'];

            $this->connection = new \PDO(
                $dsn,
                $this->config['db_user'],
                $this->config['db_pass'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_PERSISTENT => false
                ]
            );
        } catch (\PDOException $e) {
            throw new \Exception("Error de conexión a base de datos: " . $e->getMessage());
        }
    }

    /**
     * Obtener conexión PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Ejecutar consulta preparada
     */
    public function query($sql, $params = [])
    {
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($params);
            return $statement;
        } catch (\PDOException $e) {
            throw new \Exception("Error en consulta SQL: " . $e->getMessage());
        }
    }

    /**
     * Obtener un registro
     */
    public function getOne($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->fetch();
    }

    /**
     * Obtener todos los registros
     */
    public function getAll($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->fetchAll();
    }

    /**
     * Insertar registro
     */
    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }

    /**
     * Actualizar registro
     */
    public function update($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->rowCount();
    }

    /**
     * Eliminar registro
     */
    public function delete($sql, $params = [])
    {
        $statement = $this->query($sql, $params);
        return $statement->rowCount();
    }

    /**
     * Prevenir clonación
     */
    private function __clone() {}

    /**
     * Prevenir deserialización
     */
    public function __wakeup() {}
}
