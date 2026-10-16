<?php
declare(strict_types=1);

namespace App\Models;

/**
 * All SQL used by Flower. Table and column names come from the constants
 * of Flower / Region, never from plain strings.
 */
class FlowerQuery
{
    // SELECT
    public static string $GetAll    = "SELECT * FROM " . Flower::TABLE_LABEL . " ORDER BY " . Flower::ID_LABEL;
    public static string $GetBySlug = "SELECT * FROM " . Flower::TABLE_LABEL . " WHERE " . Flower::SLUG_LABEL . " = ?";

    // SEARCH conditions (the flowers table is aliased "f" in Search())
    public static string $WhereRegion =
        "EXISTS (SELECT 1 FROM " . Flower::FLOWER_REGION_TABLE_LABEL . " fr"
        . " JOIN " . Region::TABLE_LABEL . " r ON r." . Region::ID_LABEL . " = fr." . Flower::REGION_ID_LABEL
        . " WHERE fr." . Flower::FLOWER_ID_LABEL . " = f." . Flower::ID_LABEL
        . " AND r." . Region::NAME_LABEL . " = ?)";

    public static string $WhereEdible = "f." . Flower::EDIBLE_LABEL . " = ?";

    public static string $WhereTerm =
        "(f." . Flower::NAME_LABEL . " LIKE ?"
        . " OR f." . Flower::SCIENTIFIC_NAME_LABEL . " LIKE ?"
        . " OR f." . Flower::DESCRIPTION_LABEL . " LIKE ?)";

    /** Builds the search query from the WHERE conditions above (joined with AND). */
    public static function Search(array $conditions): string
    {
        return "SELECT f.* FROM " . Flower::TABLE_LABEL . " f"
            . ($conditions ? " WHERE " . implode(" AND ", $conditions) : "")
            . " ORDER BY f." . Flower::ID_LABEL;
    }

    /** The first $n flowers. $n is cast to int, so it is safe to put in LIMIT. */
    public static function GetFirst(int $n): string
    {
        return "SELECT * FROM " . Flower::TABLE_LABEL
            . " ORDER BY " . Flower::ID_LABEL . " ASC LIMIT " . max(1, $n);
    }

    /** The $n newest flowers, newest first. */
    public static function GetRecent(int $n): string
    {
        return "SELECT * FROM " . Flower::TABLE_LABEL
            . " ORDER BY " . Flower::ID_LABEL . " DESC LIMIT " . max(1, $n);
    }

    // RELATED DATA, loaded for several flowers at once: pass the number of flower ids.
    public static function GetRegionsByFlowerIds(int $count): string
    {
        return "SELECT fr." . Flower::FLOWER_ID_LABEL . ", r." . Region::NAME_LABEL
            . " FROM " . Flower::FLOWER_REGION_TABLE_LABEL . " fr"
            . " JOIN " . Region::TABLE_LABEL . " r ON r." . Region::ID_LABEL . " = fr." . Flower::REGION_ID_LABEL
            . " WHERE fr." . Flower::FLOWER_ID_LABEL . " IN (" . self::Placeholders($count) . ")"
            . " ORDER BY r." . Region::ID_LABEL;
    }

    public static function GetColorsByFlowerIds(int $count): string
    {
        return "SELECT " . Flower::FLOWER_ID_LABEL . ", " . Flower::HEX_LABEL
            . " FROM " . Flower::FLOWER_COLORS_TABLE_LABEL
            . " WHERE " . Flower::FLOWER_ID_LABEL . " IN (" . self::Placeholders($count) . ")"
            . " ORDER BY " . Flower::SORT_ORDER_LABEL . ", " . Flower::ID_LABEL;
    }

    public static function GetImagesByFlowerIds(int $count): string
    {
        return "SELECT " . Flower::FLOWER_ID_LABEL . ", " . Flower::FILE_LABEL
            . " FROM " . Flower::FLOWER_IMAGES_TABLE_LABEL
            . " WHERE " . Flower::FLOWER_ID_LABEL . " IN (" . self::Placeholders($count) . ")"
            . " ORDER BY " . Flower::SORT_ORDER_LABEL . ", " . Flower::ID_LABEL;
    }

    public static function GetLinksByFlowerIds(int $count): string
    {
        return "SELECT " . Flower::FLOWER_ID_LABEL . ", " . Flower::PLATFORM_LABEL . ", " . Flower::URL_LABEL
            . " FROM " . Flower::FLOWER_LINKS_TABLE_LABEL
            . " WHERE " . Flower::FLOWER_ID_LABEL . " IN (" . self::Placeholders($count) . ")"
            . " ORDER BY " . Flower::ID_LABEL;
    }

    /** "?,?,?" for an IN (...) list. */
    private static function Placeholders(int $count): string
    {
        return implode(",", array_fill(0, max(1, $count), "?"));
    }
}