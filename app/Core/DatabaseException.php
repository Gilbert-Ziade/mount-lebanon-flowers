<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Thrown when the database cannot be reached. public/index.php catches it
 * (together with PDOException for failed queries) and shows the error page.
 */
class DatabaseException extends RuntimeException
{
}
