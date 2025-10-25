<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

abstract class Model
{
    protected static string $table;

    protected static function db(): PDO
    {
        static $pdo;
        if (!$pdo) {
            global $container;
            /** @var Database $database */
            $database = $container['db'];
            $pdo = $database->pdo();
        }
        return $pdo;
    }

    public static function all(): array
    {
        $stmt = self::db()->query('SELECT * FROM ' . static::$table . ' ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(array $data): int
    {
        $fields = array_keys($data);
        $columns = implode(', ', $fields);
        $placeholders = implode(', ', array_map(fn($f) => ':' . $f, $fields));
        $stmt = self::db()->prepare('INSERT INTO ' . static::$table . ' (' . $columns . ') VALUES (' . $placeholders . ')');
        $stmt->execute($data);
        return (int) self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $set = implode(', ', array_map(fn($f) => $f . ' = :' . $f, array_keys($data)));
        $data['id'] = $id;
        $stmt = self::db()->prepare('UPDATE ' . static::$table . ' SET ' . $set . ' WHERE id = :id');
        return $stmt->execute($data);
    }

    public static function delete(int $id): bool
    {
        $stmt = self::db()->prepare('DELETE FROM ' . static::$table . ' WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function paginate(int $perPage = 25, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = self::db()->prepare('SELECT SQL_CALC_FOUND_ROWS * FROM ' . static::$table . ' ORDER BY id DESC LIMIT :offset, :limit');
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
        $total = (int) self::db()->query('SELECT FOUND_ROWS()')->fetchColumn();
        return ['data' => $items, 'total' => $total, 'per_page' => $perPage, 'current_page' => $page];
    }
}
