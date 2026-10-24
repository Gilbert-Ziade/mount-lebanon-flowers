<?php
declare(strict_types=1);

namespace App\Security\JWT;

use App\Core\Response;
use App\Security\JWT\AuthTokenService;

/** Used by the Router: runs before the controller of every protected route. */
class AuthMiddleware
{
    public function validateJWT(): void
    {
        if (AuthTokenService::validate($_COOKIE[AuthTokenService::COOKIE] ?? null) === null) {
            Response::redirect(url('/login')); // exits
        }
    }
}