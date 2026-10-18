<?php // Platform.php
declare(strict_types=1);

namespace App\Models\Platform;

use App\Core\DbConnection;

class Platform
{
    public const TABLE_LABEL = 'platforms';
    public const ID_LABEL    = 'id';
    public const NAME_LABEL  = 'name';

    /** @return array<int, array{id:int, name:string}> */
    public static function all(): array
    {
        return DbConnection::select(PlatformQuery::$GetAll);
    }
}
