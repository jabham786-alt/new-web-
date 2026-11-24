<?php
declare(strict_types=1);

/**
 * Return the current session's CSRF token, creating one when necessary.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate a submitted CSRF token using a timing-safe comparison.
 * Accept mixed input so malformed array submissions are rejected, not fatal.
 */
function csrf_token_is_valid(mixed $submittedToken): bool
{
    $knownToken = $_SESSION['csrf_token'] ?? null;

    return is_string($knownToken)
        && is_string($submittedToken)
        && $submittedToken !== ''
        && hash_equals($knownToken, $submittedToken);
}

/**
 * Reject non-POST requests and requests with missing/invalid CSRF tokens.
 * Call this at the start of every state-changing POST endpoint.
 */
function require_valid_csrf_post(): void
{
    $submittedToken = $_POST['csrf_token'] ?? null;

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'
        || !csrf_token_is_valid($submittedToken)) {
        http_response_code(403);
        exit('Request rejected.');
    }
}
