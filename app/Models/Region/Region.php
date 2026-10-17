<?php
declare(strict_types=1);

namespace App\Models\Region;

use App\Core\DbConnection;

class Region
{
    public const TABLE_LABEL = 'regions';
    public const ID_LABEL    = 'id';
    public const NAME_LABEL  = 'name';
    public const SLUG_LABEL  = 'slug';

    /** Key of the flower count in withCounts() results (also the SQL alias). */
    public const COUNT_LABEL = 'count';

    //------------------------------------ Get region names ------------------------------------//
    /** @return string[] Region names in display order (by id). */
    public static function names(): array
    {
        static $names = null;
        return $names ??= array_column(
            DbConnection::select(RegionQuery::$GetNames),
            self::NAME_LABEL
        );
    }

    //------------------------------------ Get regions with flower counts ------------------------------------//
    /** @return array<int, array{name:string, count:int}> */
    public static function withCounts(): array
    {
        return array_map(fn (array $r): array => [
            self::NAME_LABEL  => $r[self::NAME_LABEL],
            self::COUNT_LABEL => (int) $r[self::COUNT_LABEL],
        ], DbConnection::select(RegionQuery::$GetWithCounts));
    }
}