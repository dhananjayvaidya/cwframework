<?php

declare(strict_types=1);

namespace CWF\Database;

use PDO;

/**
 * Modern Query Builder with PHP 8.5 features
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class QueryBuilder
{
    private ?string $table = null;
    private array $select = ['*'];
    private array $where = [];
    private array $orWhere = [];
    private array $joins = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private ?string $having = null;
    private ?int $limit = null;
    private ?int $offset = null;
    private array $bindings = [];

    public function __construct(
        private readonly PDO $connection,
        ?string $table = null
    ) {
        $this->table = $table;
    }

    /**
     * Set table
     */
    public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Select columns
     */
    public function select(string|array $columns = ['*']): self
    {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add where clause
     */
    public function where(string|array $column, mixed $operator = null, mixed $value = null): self
    {
        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->where($key, '=', $val);
            }
            return $this;
        }

        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = [$column, $operator, $value];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add OR where clause
     */
    public function orWhere(string $column, mixed $operator = null, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->orWhere[] = [$column, $operator, $value];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Where IN clause
     */
    public function whereIn(string $column, array $values): self
    {
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->where[] = [$column, 'IN', "($placeholders)"];
        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    /**
     * Where NULL clause
     */
    public function whereNull(string $column): self
    {
        $this->where[] = [$column, 'IS', 'NULL'];
        return $this;
    }

    /**
     * Where NOT NULL clause
     */
    public function whereNotNull(string $column): self
    {
        $this->where[] = [$column, 'IS NOT', 'NULL'];
        return $this;
    }

    /**
     * Join table
     */
    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = [
            'type' => $type,
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second
        ];

        return $this;
    }

    /**
     * Left join
     */
    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Right join
     */
    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    /**
     * Order by
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [$column, strtoupper($direction)];
        return $this;
    }

    /**
     * Group by
     */
    public function groupBy(string|array $columns): self
    {
        $this->groupBy = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Having clause
     */
    public function having(string $condition): self
    {
        $this->having = $condition;
        return $this;
    }

    /**
     * Limit
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Offset
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get all records
     */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetchAll();
    }

    /**
     * Get first record
     */
    public function first(): ?array
    {
        $this->limit(1);
        $result = $this->get();

        return $result[0] ?? null;
    }

    /**
     * Get single value
     */
    public function value(string $column): mixed
    {
        $result = $this->first();
        return $result[$column] ?? null;
    }

    /**
     * Find by ID
     */
    public function find(int|string $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Count records
     */
    public function count(): int
    {
        $this->select = ['COUNT(*) as count'];
        $result = $this->first();

        return (int) ($result['count'] ?? 0);
    }

    /**
     * Check if record exists
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Insert record
     */
    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $values = array_values($data);
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(',', $columns),
            $placeholders
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($values);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Insert multiple records
     */
    public function insertMany(array $records): int
    {
        if (empty($records)) {
            return 0;
        }

        $columns = array_keys($records[0]);
        $placeholders = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
        $allPlaceholders = implode(',', array_fill(0, count($records), $placeholders));

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES %s",
            $this->table,
            implode(',', $columns),
            $allPlaceholders
        );

        $values = [];
        foreach ($records as $record) {
            $values = array_merge($values, array_values($record));
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    /**
     * Update records
     */
    public function update(array $data): int
    {
        $sets = [];
        $values = [];

        foreach ($data as $column => $value) {
            $sets[] = "$column = ?";
            $values[] = $value;
        }

        $sql = sprintf("UPDATE %s SET %s", $this->table, implode(', ', $sets));

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
            $values = array_merge($values, $this->bindings);
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($values);

        return $stmt->rowCount();
    }

    /**
     * Delete records
     */
    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }

    /**
     * Increment column
     */
    public function increment(string $column, int $amount = 1): int
    {
        return $this->update([$column => new \PDO("$column + $amount")]);
    }

    /**
     * Decrement column
     */
    public function decrement(string $column, int $amount = 1): int
    {
        return $this->update([$column => new \PDO("$column - $amount")]);
    }

    /**
     * Build SELECT query
     */
    private function buildSelectQuery(): string
    {
        $sql = sprintf("SELECT %s FROM %s", implode(', ', $this->select), $this->table);

        // Joins
        foreach ($this->joins as $join) {
            $sql .= sprintf(
                " %s JOIN %s ON %s %s %s",
                $join['type'],
                $join['table'],
                $join['first'],
                $join['operator'],
                $join['second']
            );
        }

        // Where
        if (!empty($this->where) || !empty($this->orWhere)) {
            $sql .= ' WHERE ' . $this->buildWhereClause();
        }

        // Group By
        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        // Having
        if ($this->having) {
            $sql .= ' HAVING ' . $this->having;
        }

        // Order By
        if (!empty($this->orderBy)) {
            $orders = array_map(fn($order) => "{$order[0]} {$order[1]}", $this->orderBy);
            $sql .= ' ORDER BY ' . implode(', ', $orders);
        }

        // Limit
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        // Offset
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    /**
     * Build WHERE clause
     */
    private function buildWhereClause(): string
    {
        $clauses = [];

        foreach ($this->where as $condition) {
            [$column, $operator, $value] = $condition;

            if ($value === 'NULL') {
                $clauses[] = "$column $operator NULL";
            } elseif (str_contains((string)$value, '(')) {
                $clauses[] = "$column $operator $value";
            } else {
                $clauses[] = "$column $operator ?";
            }
        }

        $whereSql = implode(' AND ', $clauses);

        if (!empty($this->orWhere)) {
            $orClauses = [];
            foreach ($this->orWhere as $condition) {
                [$column, $operator, $value] = $condition;
                $orClauses[] = "$column $operator ?";
            }
            $whereSql .= ' OR ' . implode(' OR ', $orClauses);
        }

        return $whereSql;
    }

    /**
     * Dump query for debugging
     */
    public function toSql(): string
    {
        return $this->buildSelectQuery();
    }
}
