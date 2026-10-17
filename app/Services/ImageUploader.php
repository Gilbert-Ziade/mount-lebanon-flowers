<?php
declare(strict_types=1);

namespace App\Services;

class ImageUploader
{
    private const ALLOWED   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const MAX_BYTES = 5 * 1024 * 1024; // 5 MB per image

    public static function dir(): string
    {
        return BASE_PATH . '/public/assets/img/flowers/';
    }

    /**
     * Saves every valid file sent in <input type="file" name="$field[]" multiple>.
     * @return string[] the new file names (what goes in the DB)
     */
    public static function saveMany(string $field): array
    {
        $f = $_FILES[$field] ?? null;
        if (!$f || !is_array($f['name'])) {
            return [];
        }
        if (!is_dir(self::dir())) {
            mkdir(self::dir(), 0755, true);
        }

        $saved = [];
        foreach ($f['name'] as $i => $_) {
            $tmp = $f['tmp_name'][$i];
            if ($f['error'][$i] !== UPLOAD_ERR_OK || $f['size'][$i] > self::MAX_BYTES || !is_uploaded_file($tmp)) {
                continue;
            }
            // Trust the real content, not the name or the browser's Content-Type
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($tmp);
            if (!isset(self::ALLOWED[$mime]) || @getimagesize($tmp) === false) {
                continue;
            }
            $name = bin2hex(random_bytes(8)) . '.' . self::ALLOWED[$mime]; // never reuse the user's name
            if (move_uploaded_file($tmp, self::dir() . $name)) {
                $saved[] = $name;
            }
        }
        return $saved;
    }

    /** Deletes one image from disk. External URLs are ignored. */
    public static function delete(string $file): void
    {
        if (preg_match('#^(https?:)?//|^/#', $file)) {
            return;
        }
        $path = self::dir() . basename($file); // basename() blocks ../ tricks
        if (is_file($path)) {
            unlink($path);
        }
    }
}