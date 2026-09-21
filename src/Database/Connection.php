<?php

namespace App\Database;

use App\Config\Env;
use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    private static ?PDO $instance = null;

    public static function get(): PDO
    {
        if (self::$instance === null) {
            $host = Env::get('DB_HOST', 'mysql');
            $port = Env::get('DB_PORT', 3306);
            $database = Env::get('DB_DATABASE', 'eleitor_consciente');
            $username = Env::get('DB_USERNAME', 'eleitor_user');
            $password = Env::get('DB_PASSWORD', 'eleitor_pass');

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]);
            } catch (PDOException $e) {
                // Log sem vazar credenciais
                error_log("Database connection error: " . $e->getMessage());
                throw new RuntimeException("Falha na conexão com o banco de dados. Verifique a configuração.");
            }
        }

        return self::$instance;
    }

    public static function setInstance(?PDO $pdo): void
    {
        self::$instance = $pdo;
    }
}

