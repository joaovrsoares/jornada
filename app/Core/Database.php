<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

class Database {
    private static ?PDO $connection = null;

    public static function connection(): PDO {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/database.php';

            # Cria o DSN (Data Source Name) para a conexão com o banco de dados
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'],
                $config['database']
            );

            # Cria a conexão com o banco de dados usando PDO
            self::$connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$connection;
    }
}