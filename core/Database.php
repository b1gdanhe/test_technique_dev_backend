<?php

namespace Core;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $config = require base_path('config/database.php');

        $this->connection = new \PDO(
            "{$config['db_system']}:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
            $config['db_username'],
            $config['db_password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this->connection, $table);
    }
}

class QueryBuilder
{
    private $connection;
    private $table;
    private $type;
    private $columns;
    private $where;

    public function __construct(\PDO $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    public function select(array $columns = ['*']): self
    {
        $this->type = 'select';
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->where[] = compact('column', 'operator', 'value');
        return $this;
    }

    public function first()
    {
        $query = $this->buildQuery();
        $query .= " LIMIT 1";

        $statement = $this->connection->prepare($query);
        $statement->execute($this->buildParams());

        return $statement->fetch();
    }

    public function get(): array
    {
        $query = $this->buildQuery();
        $statement = $this->connection->prepare($query);
        $statement->execute($this->buildParams());

        return $statement->fetchAll();
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);
        $values = array_fill(0, count($columns), '?');

        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $values)
        );

        $statement = $this->connection->prepare($query);
        $statement->execute(array_values($data));

        return (int) $this->connection->lastInsertId();
    }

    public function update(array $data): int
    {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "$column = ?";
        }

        $query = sprintf(
            "UPDATE %s SET %s",
            $this->table,
            implode(', ', $set)
        );

        $params = array_values($data);

        if (!empty($this->where)) {
            $query .= " WHERE " . $this->buildWhereClause();
            $params = array_merge($params, $this->buildWhereParams());
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($params);

        return $statement->rowCount();
    }

    private function buildQuery(): string
    {
        $query = sprintf(
            "SELECT %s FROM %s",
            implode(', ', $this->columns),
            $this->table
        );

        if (!empty($this->where)) {
            $query .= " WHERE " . $this->buildWhereClause();
        }

        return $query;
    }

    private function buildWhereClause(): string
    {
        return implode(' AND ', array_map(function ($condition) {
            return "{$condition['column']} {$condition['operator']} ?";
        }, $this->where));
    }

    private function buildParams(): array
    {
        if (empty($this->where)) {
            return [];
        }

        return $this->buildWhereParams();
    }

    private function buildWhereParams(): array
    {
        return array_map(function ($condition) {
            return $condition['value'];
        }, $this->where);
    }
}
