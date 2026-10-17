<?php

declare(strict_types=1);

/** Read a value from config/config.php, e.g. config('social.instagram'). */
function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    $config ??= require BASE_PATH . '/config/config.php';

    $value = $config;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** Escape a value for HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Build an internal URL: url('/flowers') */
function url(string $path = '/'): string
{
    return rtrim((string) config('base_url', ''), '/') . '/' . ltrim($path, '/');
}

/** Build a URL to a file inside public/assets: asset('css/base.css') */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Generated flower illustration used as a placeholder image (variant 0..2). */
function flower_art(array $colors, int $size = 120, int $variant = 0): string
{
    $c  = $colors[$variant % count($colors)] ?? $colors[0];
    $c2 = $colors[1] ?? '#f5d547';
    $n  = [8, 6, 12][$variant % 3];
    $rx = [10, 13, 6][$variant % 3];

    $petals = '';
    for ($i = 0; $i < $n; $i++) {
        $petals .= sprintf(
            '<ellipse cx="50" cy="26" rx="%d" ry="19" fill="%s" opacity=".92" transform="rotate(%s 50 50)"/>',
            $rx,
            e($c),
            round($i * 360 / $n, 2)
        );
    }
    $center = ($c2 === $c) ? '#f5d547' : $c2;

    return sprintf(
        '<svg width="%1$d" height="%1$d" viewBox="0 0 100 100" role="img" aria-label="Illustration of the flower">%2$s<circle cx="50" cy="50" r="9" fill="%3$s"/></svg>',
        $size,
        $petals,
        e($center)
    );
}

/** Slides of a flower: its real image paths, or 3 generated placeholders (0,1,2). */
function flower_slides(array $flower): array
{
    return !empty($flower['images']) ? array_values($flower['images']) : [0, 1, 2];
}

/** HTML for one slide (real <img> or generated SVG). */
function flower_slide(array $flower, int $index, int $size = 120): string
{
    $slide = flower_slides($flower)[$index] ?? null;

    if (is_string($slide)) {
        $src = preg_match('#^(https?:)?//|^/#', $slide) ? $slide : asset('img/flowers/' . $slide);
        return sprintf('<img src="%s" alt="%s" loading="lazy">', e($src), e($flower['name'] . ' photo ' . ($index + 1)));
    }
    return flower_art($flower['colors'], $size, (int) $slide);
}

/** Soft background color taken from the flower's first color. */
function flower_bg(array $flower): string
{
    return 'background:' . $flower['colors'][0] . '22';
}

function color_dots(array $flower): string
{
    $html = '<span class="dots" aria-label="Colors">';
    foreach ($flower['colors'] as $color) {
        $html .= '<span class="dot" style="background:' . e($color) . '"></span>';
    }
    return $html . '</span>';
}

function edible_tag(array $flower): string
{
    return $flower['edible']
        ? '<span class="tag">Edible</span>'
        : '<span class="tag no">Not edible</span>';
}

/** "Wild thyme (Za'atar)" -> "wild-thyme-za-atar" */
function slugify(string $text): string
{
    return trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-') ?: 'item';
}
