<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

require_once APP_ROOT . '/app/security/headers.php';
require_once APP_ROOT . '/app/security/helpers.php';
require_once APP_ROOT . '/app/security/csrf.php';

send_security_headers();

$isHttps = isset($_SERVER['HTTPS'])
    && strtolower((string) $_SERVER['HTTPS']) !== 'off'
    && (string) $_SERVER['HTTPS'] !== '';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

/**
 * Create a PDO connection only when a page actually needs the database.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $configPath = APP_ROOT . '/app/config/config.local.php';
    if (!is_file($configPath)) {
        throw new RuntimeException('Database configuration is not installed.');
    }

    $config = require $configPath;
    $dbConfig = $config['database'] ?? [];

    foreach (['host', 'name', 'username', 'password'] as $key) {
        if (!isset($dbConfig[$key]) || !is_string($dbConfig[$key]) || $dbConfig[$key] === ''
            || str_starts_with($dbConfig[$key], 'CHANGE_ME_')) {
            throw new RuntimeException('Database configuration is incomplete.');
        }
    }

    $charset = $dbConfig['charset'] ?? 'utf8mb4';
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['name'],
        $charset
    );

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
