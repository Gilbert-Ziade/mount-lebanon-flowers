<?php
declare(strict_types=1);

namespace App\Models;

class Region
{
    /** @return string[] */
    public static function names(): array
    {
        return config('regions', []);
    }

    /** @return array<int, array{name:string, count:int}> */
    public static function withCounts(): array
    {
        return array_map(fn (string $name) => [
            'name'  => $name,
            'count' => count(Flower::search(null, $name)),
        ], self::names());
    }
}
