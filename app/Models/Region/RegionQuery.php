<?php

declare(strict_types=1);

namespace App\Models\Region;

use App\Models\Flower\Flower;

class RegionQuery
{
    // SELECT
    public static string $GetNames = "SELECT " . Region::NAME_LABEL
        . " FROM " . Region::TABLE_LABEL
        . " ORDER BY " . Region::ID_LABEL;

    public static string $GetWithCounts = "SELECT r." . Region::NAME_LABEL
        . ", COUNT(fr." . Flower::FLOWER_ID_LABEL . ") AS " . Region::COUNT_LABEL
        . " FROM " . Region::TABLE_LABEL . " r"
        . " LEFT JOIN " . Flower::FLOWER_REGION_TABLE_LABEL . " fr ON fr." . Flower::REGION_ID_LABEL . " = r." . Region::ID_LABEL
        . " GROUP BY r." . Region::ID_LABEL . ", r." . Region::NAME_LABEL
        . " ORDER BY r." . Region::ID_LABEL;

    public static string $GetIdByName = "SELECT " . Region::ID_LABEL
        . " FROM " . Region::TABLE_LABEL
        . " WHERE " . Region::NAME_LABEL . " = ?";

    public static string $Insert = "INSERT INTO " . Region::TABLE_LABEL
        . " (" . Region::NAME_LABEL . ", " . Region::SLUG_LABEL . ") VALUES (?, ?)"
        . " RETURNING " . Region::ID_LABEL;
}
