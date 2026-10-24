<?php

declare(strict_types=1);

namespace App\Models\Admin;

class AdminQuery
{
    // SELECT
    public static string $GetByUsername = "SELECT * FROM " . Admin::TABLE_LABEL
        . " WHERE " . Admin::USERNAME_LABEL . " = ?";

    public static string $GetByEmail = "SELECT * FROM " . Admin::TABLE_LABEL
        . " WHERE LOWER(" . Admin::EMAIL_LABEL . ") = LOWER(?)";

    // INSERT / UPDATE
    /*public static string $Insert = "INSERT INTO " . Admin::TABLE_LABEL
        . " (" . Admin::USERNAME_LABEL . ", " . Admin::EMAIL_LABEL . ", " . Admin::PASSWORD_HASH_LABEL . ")"
        . " VALUES (?, ?, ?) RETURNING " . Admin::ID_LABEL;*/

    public static string $UpdatePassword = "UPDATE " . Admin::TABLE_LABEL
        . " SET " . Admin::PASSWORD_HASH_LABEL . " = ? WHERE " . Admin::ID_LABEL . " = ?";

    // PASSWORD RESETS (expiry is computed by the database, so PHP and DB time zones never disagree)
    public static string $InsertReset = "INSERT INTO " . Admin::RESET_TABLE_LABEL
        . " (" . Admin::ADMIN_ID_LABEL . ", " . Admin::TOKEN_HASH_LABEL . ", " . Admin::EXPIRES_AT_LABEL . ")"
        . " VALUES (?, ?, NOW() + INTERVAL '1 hour')";

    public static string $GetResetAdminId = "SELECT " . Admin::ADMIN_ID_LABEL
        . " FROM " . Admin::RESET_TABLE_LABEL
        . " WHERE " . Admin::TOKEN_HASH_LABEL . " = ? AND " . Admin::EXPIRES_AT_LABEL . " > NOW()";

    public static string $DeleteResetsByAdmin = "DELETE FROM " . Admin::RESET_TABLE_LABEL
        . " WHERE " . Admin::ADMIN_ID_LABEL . " = ?";

    public static string $DeleteExpiredResets = "DELETE FROM " . Admin::RESET_TABLE_LABEL
        . " WHERE " . Admin::EXPIRES_AT_LABEL . " <= NOW()";
}
