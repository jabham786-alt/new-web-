<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['app_config'] ?? [];
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) return $default;
        $value = $value[$segment];
    }
    return $value;
}

/**
 * Accept only a local absolute-path return target.
 * Reject external URLs, protocol-relative URLs, and malformed paths.
 */
function safe_return_path(string $path, string $fallback = '/'): string
{
    $path = trim($path);

    if (
        $path === '' ||
        $path[0] !== '/' ||
        str_starts_with($path, '//') ||
        preg_match('/[\\r\\n\\\\]/', $path)
    ) {
        return $fallback;
    }

    $parts = parse_url($path);

    if (
        $parts === false ||
        isset($parts['scheme']) ||
        isset($parts['host']) ||
        isset($parts['user']) ||
        isset($parts['pass'])
    ) {
        return $fallback;
    }

    return $path;
}

function redirect_local(string $path): never
{
    if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) $path = '/';
    header('Location: ' . $path, true, 303);
    exit;
}

function whatsapp_url(string $message = ''): string
{
    $number = preg_replace('/[^0-9]/', '', (string) app_config('business.whatsapp', ''));
    return 'https://wa.me/' . $number . ($message !== '' ? '?text=' . rawurlencode($message) : '');
}
