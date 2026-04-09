<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function adminSqlExportPath(): string
{
    return dirname(__DIR__) . '/storage/admin-products.sql';
}

function appendAdminSql(string $sql): void
{
    $path = adminSqlExportPath();
    $prefix = "-- " . date('Y-m-d H:i:s') . PHP_EOL;
    file_put_contents($path, $prefix . $sql . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function q($value): string
{
    // Используем PDO::quote для корректного экранирования в SQL
    $pdo = getPdo();
    if ($value === null) {
        return 'NULL';
    }
    return $pdo->quote((string) $value);
}

