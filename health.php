<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "DomZone health\n";
echo "PHP: " . PHP_VERSION . "\n";
echo "SCRIPT: " . ($_SERVER['SCRIPT_NAME'] ?? '') . "\n";
echo "DOC_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? '') . "\n";
echo "__DIR__: " . __DIR__ . "\n";
echo "db.local.php exists: " . (is_file(__DIR__ . '/includes/db.local.php') ? 'YES' : 'NO') . "\n";

try {
    getPdo()->query('SELECT 1');
    echo "DB: OK\n";
} catch (Throwable $e) {
    echo "DB: FAIL\n";
    echo $e->getMessage() . "\n";
}

