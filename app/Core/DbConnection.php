<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;

/**
 * Thin PDO wrapper. Place at: app/Core/DbConnection.php
 *
 * Usage (always with prepared statements, never concatenate user input):
 *
 *   $rows = DbConnection::select('SELECT * FROM flowers WHERE edible = ?', [1]);
 *   $row  = DbConnection::selectOne('SELECT * FROM flowers WHERE slug = ?', ['cyclamen']);
 *   $id   = DbConnection::insert('INSERT INTO flowers (slug, name) VALUES (?, ?)', ['x', 'X']);
 *   $n    = DbConnection::execute('UPDATE flowers SET name = ? WHERE id = ?', ['Y', $id]);
 *
 *   DbConnection::transaction(function () {
 *       $id = DbConnection::insert('INSERT INTO flowers ...', [...]);
 *       DbConnection::execute('INSERT INTO flower_images ...', [$id, 'a.jpg']);
 *   });
 */
class DbConnection
{
    private static ?PDO $pdo = null;

    /** Connection, opened lazily on first use. */
    /*public static function connection(): PDO
    {
        if (self::$pdo === null) {
            // DB_* constants are defined by config/config.php, which config() loads once.
            config('site_name');

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                DB_HOST,
                DB_PORT ?: '3306',
                DB_NAME
            );

            try {
                self::$pdo = new PDO($dsn, (string) DB_USER, (string) DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
                ]);
            } catch (PDOException $e) {
                // Log the real reason, show a generic message to the visitor.
                error_log('DB connection failed: ' . $e->getMessage());
                throw new RuntimeException('Database connection failed.', 0, $e);
            }
        }

        return self::$pdo;
    }*/

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            config('site_name');

            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                DB_HOST,
                DB_PORT ?: '5432',
                DB_NAME
            );

            try {
                self::$pdo = new PDO(
                    $dsn,
                    (string) DB_USER,
                    (string) DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                error_log(
                    'DB connection failed: ' . $e->getMessage()
                );

                throw new RuntimeException(
                    'Database connection failed.',
                    0,
                    $e
                );
            }
        }

        return self::$pdo;
    }

    public static function close(): void
    {
        self::$pdo = null;
    }

    /** SELECT many rows. Returns [] when nothing matches. */
    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** SELECT a single row. Returns null when nothing matches. */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** SELECT a single value (first column of first row), e.g. COUNT(*). */
    public static function scalar(string $sql, array $params = []): mixed
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();
        return $value === false ? null : $value;
    }

    /** INSERT / UPDATE / DELETE. Returns the number of affected rows. */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** INSERT and return the new auto-increment id. */
    public static function insert(string $sql, array $params = []): int
    {
        self::execute($sql, $params);
        return (int) self::connection()->lastInsertId();
    }

    /**
     * Run several queries atomically: all succeed, or none are saved.
     * Returns whatever the callback returns. Nested calls join the outer transaction.
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connection();

        if ($pdo->inTransaction()) {
            return $callback();
        }

        $pdo->beginTransaction();
        try {
            $result = $callback();
            $pdo->commit();
            return $result;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
