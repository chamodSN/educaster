<?php
// common/helpers.php

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Echo this inside every <form method="POST"> ... </form> */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

/** Call at the top of every POST handler before touching the database */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(400);
        die('Your session has expired or this form was submitted from an untrusted source. Please go back, refresh the page, and try again.');
    }
}

function render_stars(float $rating, int $max = 5): string
{
    $rating = max(0, min($max, $rating));
    $full   = (int) round($rating);
    $html   = '<span class="stars" aria-label="' . number_format($rating, 1) . ' out of ' . $max . ' stars">';
    $html  .= str_repeat('<i class="fas fa-star"></i>', $full);
    $html  .= str_repeat('<i class="far fa-star"></i>', $max - $full);
    $html  .= '</span>';
    return $html;
}

function sanitize_url(?string $url): ?string
{
    $url = trim((string) $url);
    if ($url === '') {
        return null;
    }
    if (!preg_match('#^https?://#i', $url)) {
        return null;
    }
    // Reject anything that still smells like a scheme trick after the check above.
    if (preg_match('/[\x00-\x1F]/', $url)) {
        return null;
    }
    return $url;
}

function allowed_video_extension(string $ext): bool
{
    return in_array(strtolower($ext), ['mp4', 'mov', 'avi', 'webm'], true);
}

function allowed_resource_extension(string $ext): bool
{
    return in_array(strtolower($ext), ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'jpg', 'jpeg', 'png', 'gif'], true);
}

function allowed_image_extension(string $ext): bool
{
    return in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
}

/**
 * Moves an uploaded file into /uploads after validating its extension.
 * Returns the new stored filename, or null if no file / invalid file.
 */
function handle_upload(string $fieldName, callable $extensionCheck, string $prefix): ?string
{
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if (!$extensionCheck($ext)) {
        return null;
    }
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $newName = uniqid($prefix . '_', true) . '.' . $ext;
    if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadDir . $newName)) {
        return $newName;
    }
    return null;
}

/** Deletes a previously uploaded file (used when replacing images/videos). */
function delete_upload(?string $filename): void
{
    if (!$filename) {
        return;
    }
    $path = __DIR__ . '/../uploads/' . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}

function format_date(?string $date, string $format = 'M j, Y'): string
{
    if (!$date) {
        return '—';
    }
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : '—';
}

function role_label(string $type): string
{
    $roles = ['TCH' => 'Teacher', 'INS' => 'Course Provider', 'SADMIN' => 'Super Admin', 'STD' => 'Student'];
    return $roles[$type] ?? 'User';
}

function truncate_text(?string $text, int $length = 150): string
{
    $text = trim((string) $text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return rtrim(substr($text, 0, $length)) . '…';
}