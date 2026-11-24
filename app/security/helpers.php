<?php
declare(strict_types=1);

/**
 * Escape untrusted text for an HTML text or quoted-attribute context.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirect only to a local path to avoid open-redirect vulnerabilities.
 */
function redirect_local(string $path): never
{
    if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) {
        $path = '/';
    }

    header('Location: ' . $path, true, 303);
    exit;
}
