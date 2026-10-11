<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Every method returns MOCK data from app/Data/flowers.php.
 * When you add a database, only the bodies of these methods change.
 */
class Flower
{
    /** @return array<int, array> */
    public static function all(): array
    {
        static $flowers = null;
        return $flowers ??= require BASE_PATH . '/app/Data/flowers.php';
    }

    public static function findBySlug(string $slug): ?array
    {
        foreach (self::all() as $flower) {
            if ($flower['slug'] === $slug) {
                return $flower;
            }
        }
        return null;
    }

    /**
     * @param string|null $edible "true", "false" or empty (any)
     * @return array<int, array>
     */
    public static function search(?string $term = null, ?string $region = null, ?string $edible = null): array
    {
        $term = trim((string) $term);

        return array_values(array_filter(self::all(), function (array $f) use ($term, $region, $edible): bool {
            if ($region && !in_array($region, $f['regions'], true)) {
                return false;
            }
            if ($edible === 'true' || $edible === 'false') {
                if ($f['edible'] !== ($edible === 'true')) {
                    return false;
                }
            }
            if ($term !== '') {
                $haystack = $f['name'] . ' ' . $f['scientific_name'] . ' ' . $f['description'];
                if (mb_stripos($haystack, $term) === false) {
                    return false;
                }
            }
            return true;
        }));
    }

    /** The first $n flowers (used for the hero bouquet). */
    public static function first(int $n): array
    {
        return array_slice(self::all(), 0, $n);
    }

    /** The $n most recently added flowers, newest first. */
    public static function recent(int $n): array
    {
        return array_reverse(array_slice(self::all(), -$n));
    }
}
