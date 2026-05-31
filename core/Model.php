<?php

declare(strict_types=1);

namespace Core;

use PDO;

class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';

    private PDO $db;
    private string $selectClause = '*';
    private array $whereClauses = [];
    private array $bindings = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private string $orderBy = '';

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function select(string|array $columns = '*'): static
    {
        if (is_array($columns)) {
            $this->selectClause = implode(', ', $columns);
        } else {
            $this->selectClause = $columns;
        }
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): static
    {
        $this->whereClauses[] = "{$column} {$operator} :{$column}";
        $this->bindings[":{$column}"] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy = " ORDER BY {$column} " . strtoupper($direction);
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = "SELECT {$this->selectClause} FROM {$this->table}";

        if (!empty($this->whereClauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->whereClauses);
        }

        if ($this->orderBy !== '') {
            $sql .= $this->orderBy;
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindings);

        $this->resetQuery();

        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $result = $this->limit(1)->get();
        return $result[0] ?? null;
    }

    public function insert(array $data): int|string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($col) => ":{$col}", array_keys($data)));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $bindings = [];
        foreach ($data as $key => $value) {
            $bindings[":{$key}"] = $value;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);

        $this->resetQuery();

        if (isset($data['id'])) {
            return $data['id'];
        }

        try {
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    public function update(array $data): bool
    {
        $setClauses = [];
        $bindings = [];

        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = :set_{$key}";
            $bindings[":set_{$key}"] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses);

        if (!empty($this->whereClauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->whereClauses);
        }

        $bindings = array_merge($bindings, $this->bindings);

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($bindings);

        $this->resetQuery();

        return $result;
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->whereClauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->whereClauses);
        }

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($this->bindings);

        $this->resetQuery();

        return $result;
    }

    private function resetQuery(): void
    {
        $this->selectClause = '*';
        $this->whereClauses = [];
        $this->bindings = [];
        $this->limit = null;
        $this->offset = null;
        $this->orderBy = '';
    }
}
