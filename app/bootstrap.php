<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/app/security/headers.php';
require_once APP_ROOT . '/app/security/helpers.php';
require_once APP_ROOT . '/app/security/csrf.php';

$configPath = APP_ROOT . '/app/config/config.local.php';
if (!is_file($configPath)) $configPath = APP_ROOT . '/app/config/config.example.php';
$GLOBALS['app_config'] = require $configPath;
date_default_timezone_set((string) app_config('app.timezone', 'Asia/Karachi'));
send_security_headers();

$isHttps = isset($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off' && (string) $_SERVER['HTTPS'] !== '';
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$isHttps,'httponly'=>true,'samesite'=>'Lax']);
    session_start();
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $cfg = app_config('database', []);
    foreach (['host','name','username','password'] as $key) {
        if (!isset($cfg[$key]) || !is_string($cfg[$key]) || $cfg[$key] === '' || str_starts_with($cfg[$key], 'CHANGE_ME_')) {
            throw new RuntimeException('Database configuration is incomplete.');
        }
    }
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['name'], $cfg['charset'] ?? 'utf8mb4');
    $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false,
    ]);
    return $pdo;
}
