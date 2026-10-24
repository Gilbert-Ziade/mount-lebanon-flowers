<?php
declare(strict_types=1);

namespace App\Models\Admin;

use App\Core\DbConnection;

class Admin
{
    // ---- users table ----
    public const TABLE_LABEL         = 'admins';
    public const ID_LABEL            = 'id';
    public const USERNAME_LABEL      = 'username';
    public const EMAIL_LABEL         = 'email';
    public const PASSWORD_HASH_LABEL = 'password';

    // ---- password_resets table ----
    public const RESET_TABLE_LABEL = 'password_resets';
    public const USER_ID_LABEL     = 'user_id';
    public const TOKEN_HASH_LABEL  = 'token_hash';
    public const EXPIRES_AT_LABEL  = 'expires_at';

    //------------------------------------ Find ------------------------------------//
    public static function findByEmail(string $email): ?array
    {
        return DbConnection::selectOne(AdminQuery::$GetByEmail, [$email]);
    }

    //------------------------------------ Create (used by bin/create-admin.php only) ------------------------------------//
    /* TODO 
    public static function create(string $username, string $email, string $password): int
    {
        return (int) DbConnection::scalar(AdminQuery::$Insert, [
            $username,
            strtolower($email),
            password_hash($password, PASSWORD_DEFAULT),
        ]);
    }*/

    //------------------------------------ Login ------------------------------------//
    /** The user row when username + password match, otherwise null. */
    public static function verifyLogin(string $username, string $password): ?array
    {
        static $dummy = null;
        $dummy ??= password_hash('not-a-real-password', PASSWORD_DEFAULT);

        $user = DbConnection::selectOne(AdminQuery::$GetByUsername, [$username]);

        // Always run one password_verify so an unknown username takes as long as a wrong password.
        $ok = password_verify($password, $user[self::PASSWORD_HASH_LABEL] ?? $dummy);

        return ($user !== null && $ok) ? $user : null;
    }

    //------------------------------------ Password reset ------------------------------------//
    /** Creates a one-hour reset token and returns it (only its hash is stored). */
    public static function createResetToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));

        DbConnection::transaction(function () use ($userId, $token): void {
            DbConnection::execute(AdminQuery::$DeleteExpiredResets);
            DbConnection::execute(AdminQuery::$DeleteResetsByAdmin, [$userId]); // one live token per user
            DbConnection::execute(AdminQuery::$InsertReset, [$userId, hash('sha256', $token)]);
        });

        return $token;
    }

    public static function isValidResetToken(string $token): bool
    {
        return self::resetAdminId($token) !== null;
    }

    /** Sets the new password and burns every reset token of that user. False if the token is bad/expired. */
    public static function resetPassword(string $token, string $newPassword): bool
    {
        $userId = self::resetAdminId($token);
        if ($userId === null) {
            return false;
        }

        DbConnection::transaction(function () use ($userId, $newPassword): void {
            DbConnection::execute(AdminQuery::$UpdatePassword, [password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
            DbConnection::execute(AdminQuery::$DeleteResetsByAdmin, [$userId]);
        });

        return true;
    }

    private static function resetAdminId(string $token): ?int
    {
        if ($token === '') {
            return null;
        }
        $id = DbConnection::scalar(AdminQuery::$GetResetAdminId, [hash('sha256', $token)]);
        return $id === null ? null : (int) $id;
    }
}