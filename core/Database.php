<?php

namespace Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Database – Singleton PDO wrapper (MySQL / MariaDB)
 *
 * Uses explicit bindValue() with the correct PDO param type so that
 * LIMIT / OFFSET bound parameters work correctly on all MariaDB versions.
 * With EMULATE_PREPARES off + PARAM_INT, MariaDB receives a real integer
 * in the binary protocol and accepts it in LIMIT/OFFSET positions.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $cfg = require BASE_PATH . '/config/database.php';

        $dsn = "mysql:host={$cfg['host']};dbname={$cfg['dbname']};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        ];

        try {
            $this->pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    private function __clone() {}

    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Bind a single value with the correct PDO type.
     *
     * - int   → PARAM_INT   (required for LIMIT / OFFSET on MariaDB)
     * - bool  → PARAM_BOOL
     * - null  → PARAM_NULL
     * - other → PARAM_STR
     */
    private function pdoType(mixed $value): int
    {
        return match(true) {
            is_int($value)  => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value) => PDO::PARAM_NULL,
            default         => PDO::PARAM_STR,
        };
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);

        foreach (array_values($params) as $i => $value) {
            $stmt->bindValue($i + 1, $value, $this->pdoType($value));
        }

        $stmt->execute();
        return $stmt;
    }

    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
