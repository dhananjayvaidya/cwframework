<?php

declare(strict_types=1);

namespace CWF\Database;

use PDO;
use PDOException;

/**
 * Database Manager with PHP 8.5 features
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class DatabaseManager
{
    private static ?PDO $connection = null;
    private array $config;

    // PHP 8.5 property hooks
    public QueryBuilder $table {
        get {
            if (!isset($this->table)) {
                $this->table = new QueryBuilder($this->getConnection());
            }
            return $this->table;
        }
    }

    public function __construct(array $config = [])
    {
        $this->config = $config ?: $this->loadConfig();
    }

    /**
     * Load database configuration
     */
    private function loadConfig(): array
    {
        return [
            'driver' => env('DB_DRIVER', 'mysql'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'cwframework'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        ];
    }

    /**
     * Get PDO connection
     */
    public function getConnection(): PDO
    {
        if (self::$connection === null) {
            $this->connect();
        }

        return self::$connection;
    }

    /**
     * Establish database connection
     */
    private function connect(): void
    {
        try {
            $dsn = $this->buildDsn();

            self::$connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );

            // Set charset
            if ($this->config['driver'] === 'mysql') {
                self::$connection->exec("SET NAMES {$this->config['charset']} COLLATE {$this->config['collation']}");
            }

        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Build DSN string
     */
    private function buildDsn(): string
    {
        return match($this->config['driver']) {
            'mysql' => sprintf(
                'mysql:host=%s;port=%s;dbname=%s',
                $this->config['host'],
                $this->config['port'],
                $this->config['database']
            ),
            'pgsql' => sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $this->config['host'],
                $this->config['port'],
                $this->config['database']
            ),
            'sqlite' => "sqlite:{$this->config['database']}",
            default => throw new \InvalidArgumentException("Unsupported driver: {$this->config['driver']}")
        };
    }

    /**
     * Create query builder for table
     */
    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this->getConnection(), $table);
    }

    /**
     * Execute raw query
     */
    public function raw(string $query, array $bindings = []): array|int
    {
        $stmt = $this->getConnection()->prepare($query);
        $stmt->execute($bindings);

        if (str_starts_with(strtoupper(trim($query)), 'SELECT')) {
            return $stmt->fetchAll();
        }

        return $stmt->rowCount();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }

    /**
     * Execute in transaction
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): string
    {
        return $this->getConnection()->lastInsertId();
    }
}
