<?php
declare(strict_types=1);

/**
 * Настройки БД:
 * - на хостинге (Beget) проще всего задать через файл `includes/db.local.php`
 *   (он не обязателен, но удобен) или через переменные окружения.
 * - Этот файл НЕ должен содержать “чужие” пароли в репозитории.
 */
function getPdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Локальный файл с настройками (создай на сервере): includes/db.local.php
    // Он должен вернуть массив: ['host'=>..., 'db'=>..., 'user'=>..., 'pass'=>...]
    $localConfigPath = __DIR__ . '/db.local.php';
    $local = null;
    if (is_file($localConfigPath)) {
        $local = require $localConfigPath;
        if (!is_array($local)) {
            $local = null;
        }
    }

    $env = static function (string $key): ?string {
        $v = getenv($key);
        if ($v === false) {
            return null;
        }
        $v = trim((string) $v);
        return $v === '' ? null : $v;
    };

    $pick = static function ($value): ?string {
        if ($value === null) {
            return null;
        }
        $v = trim((string) $value);
        return $v === '' ? null : $v;
    };

    $host = $pick($local['host'] ?? null) ?? $env('DB_HOST') ?? '127.0.0.1';
    // На Beget часто БД называется отдельно (например `dommzone`), а пользователь с префиксом (например `r98595b7_dommzon`)
    $db = $pick($local['db'] ?? null) ?? $env('DB_NAME') ?? 'dommzone';
    $user = $pick($local['user'] ?? null) ?? $env('DB_USER') ?? 'root';
    $pass = $pick($local['pass'] ?? null) ?? $env('DB_PASS') ?? '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (Throwable $e) {
        // На хостинге это обычно уходит в error_log / журнал ошибок.
        error_log('[DomZone] DB connect failed: ' . $e->getMessage());

        $debug = (getenv('APP_DEBUG') === '1') || (isset($_GET['debug']) && (string) $_GET['debug'] === '1');
        if ($debug) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo "DB connect failed:\n";
            echo $e->getMessage();
            exit;
        }

        throw $e;
    }

    return $pdo;
}
