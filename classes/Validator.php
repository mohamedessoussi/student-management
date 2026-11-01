<?php
// ============================================================
// classes/Validator.php
// Static validation helpers used across the application
// ============================================================

namespace Classes;

class Validator
{
    // ── Basic validators ─────────────────────────────────────

    /**
     * Checks that a value is not empty after trimming.
     */
    public static function required(mixed $value): bool
    {
        return isset($value) && trim((string)$value) !== '';
    }

    /**
     * Validates an email address format.
     */
    public static function email(string $value): bool
    {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Checks that a value contains only digits (and is positive).
     */
    public static function numeric(mixed $value): bool
    {
        return is_numeric($value) && (int)$value > 0;
    }

    // ── Image validators ─────────────────────────────────────

    /**
     * Validates uploaded image type (JPG, JPEG, PNG only).
     *
     * @param  array $file  - $_FILES element
     * @return bool
     */
    public static function imageType(array $file): bool
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        $allowedExts  = ['jpg', 'jpeg', 'png'];

        // Check MIME type via finfo for accuracy
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        return in_array($mimeType, $allowedTypes) && in_array($ext, $allowedExts);
    }

    /**
     * Validates that an uploaded file does not exceed 2 MB.
     *
     * @param  array $file  - $_FILES element
     * @return bool
     */
    public static function imageSize(array $file): bool
    {
        $maxSize = 2 * 1024 * 1024; // 2 MB in bytes
        return isset($file['size']) && $file['size'] <= $maxSize;
    }

    /**
     * Convenience wrapper: validates both type and size.
     */
    public static function image(array $file): bool
    {
        return static::imageType($file) && static::imageSize($file);
    }
}
