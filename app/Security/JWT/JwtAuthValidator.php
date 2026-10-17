<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

require_once __DIR__ . '/../../Entity/ActiveSession/ActiveSession.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '../../../Config/config.php';
require_once __DIR__ . '/../../Exceptions/ApiExceptions/UnauthorizedException.php';
require_once __DIR__ . '/../../Exceptions/ApiExceptions/BadRequestException.php';
require_once __DIR__ . '/../../Exceptions/ApiExceptions/ForbiddenException.php';
require_once __DIR__ . '/../../Consts/LangKeys.php';

class JwtAuthValidator
{
    public function validateJWT() : void
    {
        
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
            $sessionId = $decoded->session_id;
            $userId = $decoded->data->id;

            $currentSession = ActiveSession::GetActiveSessionByUserID($userId);
            
            if ($currentSession === null || $sessionId !== $currentSession->GetSessionID()) {
                throw new UnauthorizedException(LangKeys::INVALID_OR_EXPIRED_TOKEN);
            }
            else {
                $_SERVER['auth_user'] = $decoded;
            }
        } catch (ExpiredException $e) {
            throw new UnauthorizedException(LangKeys::INVALID_OR_EXPIRED_TOKEN);
        } catch (Exception $e) {
            throw new UnauthorizedException(LangKeys::INVALID_OR_EXPIRED_TOKEN);
        }
    }

    // Utility function to get decoded JWT globally
    public static function getDecodedJWT()
    {
        return $_SERVER['auth_user'];
    }
}
