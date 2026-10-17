<?php

declare(strict_types=1);

namespace App\Models\Flower;

use App\Models\Region\Region;

use App\Core\DbConnection;

/**
 * Reads flowers from MySQL. Table/column names are constants (*_LABEL) and
 * the SQL lives in FlowerQuery. Every method still returns the array shape
 * the views and the API already use:
 *
 *   id, slug, name, scientific_name, description, benefits, edible (bool),
 *   season, location, regions (string[]), colors (string[]),
 *   images (string[] file names), links (platform => url)
 */
class Flower
{
    // ---- flowers table ----
    public const TABLE_LABEL           = 'flowers';
    public const ID_LABEL              = 'id';
    public const SLUG_LABEL            = 'slug';
    public const NAME_LABEL            = 'name';
    public const SCIENTIFIC_NAME_LABEL = 'scientific_name';
    public const DESCRIPTION_LABEL     = 'description';
    public const BENEFITS_LABEL        = 'benefits';
    public const EDIBLE_LABEL          = 'edible';
    public const SEASON_LABEL          = 'season';
    public const LOCATION_LABEL        = 'location';
    public const CREATED_AT_LABEL      = 'created_at';
    public const UPDATED_AT_LABEL      = 'updated_at';

    // ---- keys of the related data inside a flower array (not DB columns) ----
    public const REGIONS_LABEL = 'regions';
    public const COLORS_LABEL  = 'colors';
    public const IMAGES_LABEL  = 'images';
    public const LINKS_LABEL   = 'links';

    // ---- related tables ----
    public const FLOWER_REGION_TABLE_LABEL = 'flower_region';
    public const FLOWER_COLORS_TABLE_LABEL = 'flower_colors';
    public const FLOWER_IMAGES_TABLE_LABEL = 'flower_images';
    public const FLOWER_LINKS_TABLE_LABEL  = 'flower_links';

    // ---- columns of the related tables ----
    public const FLOWER_ID_LABEL  = 'flower_id';
    public const REGION_ID_LABEL  = 'region_id';
    public const HEX_LABEL        = 'hex';
    public const FILE_LABEL       = 'file';
    public const SORT_ORDER_LABEL = 'sort_order';
    public const PLATFORM_LABEL   = 'platform';
    public const URL_LABEL        = 'url';

    //------------------------------------ Get all flowers ------------------------------------//
    /** @return array<int, array> */
    public static function all(): array
    {
        return self::hydrate(DbConnection::select(FlowerQuery::$GetAll));
    }

    //------------------------------------ Get flower by slug ------------------------------------//
    public static function findBySlug(string $slug): ?array
    {
        $row = DbConnection::selectOne(FlowerQuery::$GetBySlug, [$slug]);
        return $row === null ? null : self::hydrate([$row])[0];
    }

    //------------------------------------ Search flowers ------------------------------------//
    /**
     * @param string|null $edible "true", "false" or empty (any)
     * @return array<int, array>
     */
    public static function search(?string $term = null, ?string $region = null, ?string $edible = null): array
    {
        $conditions = [];
        $params     = [];
        $term       = trim((string) $term);

        if ($region) {
            $conditions[] = FlowerQuery::$WhereRegion;
            $params[]     = $region;
        }

        if ($edible === 'true' || $edible === 'false') {
            $conditions[] = FlowerQuery::$WhereEdible;
            $params[]     = $edible === 'true' ? 1 : 0;
        }

        if ($term !== '') {
            // Escape the LIKE wildcards (% and _) typed by the visitor
            $like         = '%' . addcslashes($term, '\\%_') . '%';
            $conditions[] = FlowerQuery::$WhereTerm;
            array_push($params, $like, $like, $like);
        }

        return self::hydrate(DbConnection::select(FlowerQuery::Search($conditions), $params));
    }

    //------------------------------------ First / most recent ------------------------------------//
    /** The first $n flowers (used for the hero bouquet). */
    public static function first(int $n): array
    {
        return self::hydrate(DbConnection::select(FlowerQuery::GetFirst($n)));
    }

    /** The $n most recently added flowers, newest first. */
    public static function recent(int $n): array
    {
        return self::hydrate(DbConnection::select(FlowerQuery::GetRecent($n)));
    }

    //------------------------------------ Hydrate ------------------------------------//
    /**
     * Turns flower rows into the full array shape. Related data (regions, colors,
     * images, links) is loaded with 4 queries for ALL rows, not 4 per flower.
     *
     * @param array<int, array> $rows
     * @return array<int, array>
     */
    private static function hydrate(array $rows): array
    {
        if (!$rows) {
            return [];
        }

        $ids   = array_map(fn(array $r): int => (int) $r[self::ID_LABEL], $rows);
        $count = count($ids);

        $regions = $colors = $images = $links = [];

        foreach (DbConnection::select(FlowerQuery::GetRegionsByFlowerIds($count), $ids) as $r) {
            $regions[(int) $r[self::FLOWER_ID_LABEL]][] = $r[Region::NAME_LABEL];
        }

        foreach (DbConnection::select(FlowerQuery::GetColorsByFlowerIds($count), $ids) as $r) {
            $colors[(int) $r[self::FLOWER_ID_LABEL]][] = $r[self::HEX_LABEL];
        }

        foreach (DbConnection::select(FlowerQuery::GetImagesByFlowerIds($count), $ids) as $r) {
            $images[(int) $r[self::FLOWER_ID_LABEL]][] = $r[self::FILE_LABEL];
        }

        foreach (DbConnection::select(FlowerQuery::GetLinksByFlowerIds($count), $ids) as $r) {
            $links[(int) $r[self::FLOWER_ID_LABEL]][$r[self::PLATFORM_LABEL]] = $r[self::URL_LABEL];
        }

        return array_map(function (array $r) use ($regions, $colors, $images, $links): array {
            $id = (int) $r[self::ID_LABEL];
            return [
                self::ID_LABEL              => $id,
                self::SLUG_LABEL            => $r[self::SLUG_LABEL],
                self::NAME_LABEL            => $r[self::NAME_LABEL],
                self::SCIENTIFIC_NAME_LABEL => $r[self::SCIENTIFIC_NAME_LABEL],
                self::REGIONS_LABEL         => $regions[$id] ?? [],
                self::COLORS_LABEL          => $colors[$id] ?? ['#cccccc'], // flower_art() needs at least one color
                self::EDIBLE_LABEL          => (bool) $r[self::EDIBLE_LABEL],
                self::DESCRIPTION_LABEL     => $r[self::DESCRIPTION_LABEL],
                self::BENEFITS_LABEL        => $r[self::BENEFITS_LABEL],
                self::SEASON_LABEL          => $r[self::SEASON_LABEL],
                self::LOCATION_LABEL        => $r[self::LOCATION_LABEL],
                self::IMAGES_LABEL          => $images[$id] ?? [],
                self::LINKS_LABEL           => $links[$id] ?? [],
            ];
        }, $rows);
    }

    //------------------------------------ Get flower by id ------------------------------------//
    public static function findById(int $id): ?array
    {
        $row = DbConnection::selectOne(FlowerQuery::$GetById, [$id]);
        return $row === null ? null : self::hydrate([$row])[0];
    }

    //------------------------------------ Create ------------------------------------//
    /** @param array $data keys: name, scientific_name, description, benefits, edible (0/1), season, location, instagram, regions[], colors[], images[] */
    public static function create(array $data): int
    {
        return DbConnection::transaction(function () use ($data): int {
            $id = (int) DbConnection::scalar(FlowerQuery::$Insert, [
                self::uniqueSlug($data['name']),
                $data['name'],
                $data['scientific_name'],
                $data['description'],
                $data['benefits'],
                $data['edible'],
                $data['season'],
                $data['location'],
            ]);
            self::saveRelated($id, $data);
            return $id;
        });
    }

    //------------------------------------ Update (the slug stays the same so URLs don't break) ------------------------------------//
    /** @return string[] image files removed from this flower (delete them from disk) */
    public static function update(int $id, array $data): array
    {
        $current = self::findById($id)['images'] ?? [];
        $data['remove_images'] = array_values(array_intersect($data['remove_images'] ?? [], $current));

        DbConnection::transaction(function () use ($id, $data): void {
            DbConnection::execute(FlowerQuery::$Update, [
                $data['name'],
                $data['scientific_name'],
                $data['description'],
                $data['benefits'],
                $data['edible'],
                $data['season'],
                $data['location'],
                $id,
            ]);
            self::saveRelated($id, $data);
        });

        return $data['remove_images'];
    }

    //------------------------------------ Delete ------------------------------------//
    public static function destroy(int $id): void
    {
        $images = self::findById($id)['images'] ?? [];

        DbConnection::transaction(function () use ($id): void { /* unchanged */
        });

        foreach ($images as $file) {
            \App\Services\ImageUploader::delete($file);
        }
    }

    //------------------------------------ Helpers for create/update ------------------------------------//
    /** Replaces regions, colors, images and the Instagram link of a flower. */
    private static function saveRelated(int $id, array $data): void
    {
        foreach ([self::FLOWER_REGION_TABLE_LABEL, self::FLOWER_COLORS_TABLE_LABEL] as $table) {
            DbConnection::execute(FlowerQuery::DeleteByFlowerId($table), [$id]);
        }

        foreach ($data['regions'] as $name) {
            DbConnection::execute(FlowerQuery::$InsertRegionLink, [$id, Region::findOrCreate($name)]);
        }
        foreach ($data['colors'] as $i => $hex) {
            DbConnection::execute(FlowerQuery::$InsertColor, [$id, $hex, $i]);
        }
        foreach ($data['images'] as $i => $file) {
            DbConnection::execute(FlowerQuery::$InsertImage, [$id, $file, $i]);
        }

        // Only the Instagram link is managed here; other platforms stay untouched.
        DbConnection::execute(FlowerQuery::$DeleteLinkByPlatform, [$id, 'instagram']);
        if ($data['instagram'] !== '') {
            DbConnection::execute(FlowerQuery::$InsertLink, [$id, 'instagram', $data['instagram']]);
        }

        // Images: remove the ticked ones, then append the new uploads after the existing ones.
        foreach ($data['remove_images'] ?? [] as $file) {
            DbConnection::execute(FlowerQuery::$DeleteImage, [$id, $file]);
        }
        $order = (int) DbConnection::scalar(FlowerQuery::$NextImageOrder, [$id]);
        foreach ($data['new_images'] ?? [] as $file) {
            DbConnection::execute(FlowerQuery::$InsertImage, [$id, $file, $order++]);
        }
    }

    /** "Red poppy" -> "red-poppy", or "red-poppy-2" if that slug already exists. */
    private static function uniqueSlug(string $name): string
    {
        $base = slugify($name);
        $slug = $base;
        for ($i = 2; DbConnection::scalar(FlowerQuery::$SlugExists, [$slug]) !== null; $i++) {
            $slug = $base . '-' . $i;
        }
        return $slug;
    }
}
