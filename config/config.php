<?php
// Start the session
session_start();

// Application name
define('APP_NAME', 'PSP Student Portal');

// Prevent HTML code from being displayed directly
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Redirect to another page
function redirect(string $url): never {
    header("Location: $url");
    exit;
}

// Check if user is logged in
function isLoggedIn(): bool {
    return isset($_SESSION['student_id']) || isset($_SESSION['admin_id']);
}

// Check if logged-in user is an admin
function isAdmin(): bool {
    return isset($_SESSION['admin_id']);
}

// Allow only logged-in users
function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('index.php?action=login');
    }
}

// Allow only admin users
function requireAdmin(): void {
    if (!isAdmin()) {
        redirect('index.php?action=profile');
    }
}

// Create CSRF security token
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Output a hidden CSRF input for forms
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

// Check if CSRF token is valid
function verifyCsrf(): void {
    if (!isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

// Store a message in the session
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/*
 * Get and remove the session message.
 *
 * getFlash()          -> returns ['type' => ..., 'message' => ...] or null
 * getFlash('success') -> returns the message string only if the flash is
 *                        of that type (and removes it), otherwise null
 */
function getFlash(?string $type = null): array|string|null {
    $flash = $_SESSION['flash'] ?? null;

    if ($flash === null) {
        return null;
    }

    // Filter by type: leave other types in the session for the next call
    if ($type !== null) {
        if (($flash['type'] ?? '') !== $type) {
            return null;
        }
        unset($_SESSION['flash']);
        return $flash['message'];
    }

    unset($_SESSION['flash']);
    return $flash;
}

// Store submitted form values temporarily (so a failed form can be re-shown with what the user typed)
function setOldInput(array $data): void {
    $_SESSION['old_input'] = $data;
}

// Get and remove the stored form values
function getOldInput(): array {
    $old = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);
    return $old;
}

// Check password strength: min 8 chars, uppercase, lowercase, special character
function validatePasswordStrength(string $password): ?string {

    // Check minimum length
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must contain at least one uppercase letter.';
    }

    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must contain at least one lowercase letter.';
    }

    // Check for at least one special character
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return 'Password must contain at least one special character.';
    }

    return null;
}
