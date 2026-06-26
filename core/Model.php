<?php

namespace Core;

/**
 * Base Model
 *
 * Thin active-record-inspired base class.
 * Each child declares $table and $primaryKey.
 */
abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';

    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ------------------------------------------------------------------
    // Generic CRUD
    // ------------------------------------------------------------------

    /** Find a single record by primary key */
    public function find(mixed $id): array|false
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$id]);
    }

    /** Fetch all records from the table */
    public function all(string $orderBy = '', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit > 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        return $this->db->fetchAll($sql);
    }

    /** Count all records */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) AS total FROM `{$this->table}`";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        $row = $this->db->fetchOne($sql, $params);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Insert a record.
     *
     * @param array $data  Associative array of column => value
     * @return int         Last inserted ID
     */
    public function insert(array $data): int
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})";
        $this->db->query($sql, array_values($data));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update records matching a WHERE clause.
     *
     * @param array  $data    Associative array of column => value to set
     * @param string $where   WHERE clause (without the keyword)
     * @param array  $params  Bound parameters for the WHERE clause
     */
    public function update(array $data, string $where, array $params = []): void
    {
        $setParts = array_map(fn($col) => "`{$col}` = ?", array_keys($data));
        $set      = implode(', ', $setParts);

        $sql = "UPDATE `{$this->table}` SET {$set} WHERE {$where}";
        $this->db->query($sql, [...array_values($data), ...$params]);
    }

    /**
     * Delete records matching a WHERE clause.
     */
    public function delete(string $where, array $params = []): void
    {
        $sql = "DELETE FROM `{$this->table}` WHERE {$where}";
        $this->db->query($sql, $params);
    }

    /** Delete by primary key */
    public function deleteById(mixed $id): void
    {
        $this->delete("`{$this->primaryKey}` = ?", [$id]);
    }

    // ------------------------------------------------------------------
    // Pagination helper
    // ------------------------------------------------------------------

    /**
     * Return a paginated result set.
     *
     * @return array{data: array, total: int, page: int, perPage: int, totalPages: int}
     */
    public function paginate(int $page = 1, int $perPage = 12, string $where = '', array $params = [], string $orderBy = ''): array
    {
        $total  = $this->count($where, $params);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM `{$this->table}`";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data'       => $this->db->fetchAll($sql, $params),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }
}
