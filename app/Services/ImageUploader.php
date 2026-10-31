<?php

declare(strict_types=1);

namespace App\Services;

class ImageUploader
{
    private const ALLOWED   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const MAX_BYTES = 5 * 1024 * 1024; // 5 MB per image

    private const LARGE_MAX = 1600;          // longest side, ratio kept
    private const THUMB_MAX = 640;

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
            $base = bin2hex(random_bytes(8));
            if (self::process($tmp, $mime, $base)) {
                $saved[] = $base . '.jpg';
            }
        }
        return $saved;
    }

    private static function process(string $tmp, string $mime, string $base): bool
    {
        $src = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png'  => @imagecreatefrompng($tmp),
            'image/webp' => @imagecreatefromwebp($tmp),
            default      => false,
        };
        if (!$src) {
            return false;
        }

        // Phones store rotation in EXIF: apply it so the photo is not sideways
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $angle = [3 => 180, 6 => -90, 8 => 90][@exif_read_data($tmp)['Orientation'] ?? 1] ?? 0;
            if ($angle) {
                $src = imagerotate($src, $angle, 0);
            }
        }

        $w = imagesx($src);
        $h = imagesy($src);

        // 1) Large: fit inside LARGE_MAX, keep the ratio, never upscale
        $scale = min(1, self::LARGE_MAX / max($w, $h));
        $lw = (int) round($w * $scale);
        $lh = (int) round($h * $scale);
        $large = self::canvas($lw, $lh);
        imagecopyresampled($large, $src, 0, 0, 0, 0, $lw, $lh, $w, $h);

        // 2) Thumb: center crop to 4:3, then resize
        // 2) Thumb: small copy, same proportions (no crop)
        $ts = min(1, self::THUMB_MAX / max($w, $h));
        $tw = (int) round($w * $ts);
        $th = (int) round($h * $ts);
        $thumb = self::canvas($tw, $th);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $tw, $th, $w, $h);

        $ok = imagejpeg($large, self::dir() . $base . '.jpg', 82)
            && imagejpeg($thumb, self::dir() . $base . '_thumb.jpg', 80);

        return $ok;
    }

    /** White canvas, so transparent PNGs don't turn black when saved as JPEG. */
    private static function canvas(int $w, int $h): \GdImage
    {
        $img = imagecreatetruecolor($w, $h);
        imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
        return $img;
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

        $thumb = preg_replace('/(\.\w+)$/', '_thumb$1', basename($file));
        if (is_file(self::dir() . $thumb)) {
            unlink(self::dir() . $thumb);
        }
    }
}
