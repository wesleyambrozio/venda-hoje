<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        if (!preg_match('/^[a-zA-Z0-9_,\s]+$/', $orderBy)) {
            $orderBy = 'id DESC';
        }
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $fields = $this->sanitizeColumns(array_keys($data));
        $placeholders = array_fill(0, count($fields), '?');
        $values = [];
        foreach ($fields as $field) {
            $values[] = $data[$field];
        }
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = $this->sanitizeColumns(array_keys($data));
        if ($fields === []) {
            return false;
        }
        $set = implode(' = ?, ', $fields) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$set} WHERE id = ?";
        $values = [];
        foreach ($fields as $field) {
            $values[] = $data[$field];
        }
        $values[] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** @param list<string|int> $columns */
    private function sanitizeColumns(array $columns): array
    {
        $clean = [];
        foreach ($columns as $column) {
            $name = (string) $column;
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
                $clean[] = $name;
            }
        }
        return $clean;
    }

    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
