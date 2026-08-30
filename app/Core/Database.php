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
                $config['password']
            );

            # Configura o PDO para lançar exceções em caso de erro
            self::$connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            # Configura o PDO para retornar resultados como arrays associativos
            self::$connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            # Configura o PDO para não emular prepared statements, garantindo que os parâmetros sejam tratados corretamente pelo banco de dados
            self::$connection->setAttribute(
                PDO::ATTR_EMULATE_PREPARES,
                false
            );
        }

        return self::$connection;
    }
}