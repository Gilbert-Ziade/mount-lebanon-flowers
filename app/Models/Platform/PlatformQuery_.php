<?php // PlatformQuery.php
declare(strict_types=1);

namespace App\Models\Platform;

class PlatformQuery
{
    public static string $GetAll = "SELECT " . Platform::ID_LABEL . ", " . Platform::NAME_LABEL
        . " FROM " . Platform::TABLE_LABEL
        . " ORDER BY " . Platform::ID_LABEL;
}
