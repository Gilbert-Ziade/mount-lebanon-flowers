<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Admin\Admin;
use App\Security\JWT\AuthTokenService;
use App\Services\Mailer;

class AuthController
{
    private const MIN_PASSWORD = 8;

    //------------------------------------ Login ------------------------------------//
    /** GET /login */
    public function showLogin(): string
    {
        if (AuthTokenService::validate($_COOKIE[AuthTokenService::COOKIE] ?? null) !== null) {
            Response::redirect(url('/admin'));
        }

        return $this->page('login', 'Admin login', [
            'notice'   => isset($_GET['reset']) ? 'Password updated. You can log in now.' : null,
            'error'    => null,
            'username' => '',
        ]);
    }

    /** POST /login : on success the JWT is sent as an HttpOnly cookie valid for 24h */
    public function login(): ?string
    {
        $request  = new Request();
        $username = mb_substr(trim((string) $request->input('username', '')), 0, 60);
        $password = (string) $request->input('password', '');

        $user = Admin::verifyLogin($username, $password);

        if ($user === null) {
            http_response_code(401);
            return $this->page('login', 'Admin login', [
                'notice'   => null,
                'error'    => 'Wrong username or password.',
                'username' => $username,
            ]);
        }

        $this->setCookie(AuthTokenService::issue((int) $user[Admin::ID_LABEL]), time() + AuthTokenService::LIFETIME);
        Response::redirect(url('/admin'));

        return null;
    }

    /** POST /logout */
    public function logout(): void
    {
        $this->setCookie('', time() - 3600);
        Response::redirect(url('/login'));
    }

    //------------------------------------ Forgot password ------------------------------------//
    /** GET /forgot-password */
    public function showForgot(): string
    {
        return $this->page('forgot-password', 'Forgot password', ['sent' => false]);
    }

    /** POST /forgot-password : same answer whether the email exists or not */
    public function forgot(): string
    {
        $email = mb_substr(trim((string) (new Request())->input('email', '')), 0, 190);
        $user  = $email !== '' ? Admin::findByEmail($email) : null;

        if ($user !== null) {
            $token = Admin::createResetToken((int) $user[Admin::ID_LABEL]);
            $link  = rtrim((string) config('app_url'), '/') . url('/reset-password') . '?token=' . $token;

            try {
                Mailer::send(
                    $user[Admin::EMAIL_LABEL],
                    'Reset your password',
                    "Someone asked to reset the admin password of " . config('site_name') . ".\n\n"
                    . "Open this link within 1 hour to choose a new password:\n$link\n\n"
                    . "If this was not you, ignore this email: nothing changes."
                );
            } catch (\Throwable $e) {
                error_log('Reset email failed: ' . $e->getMessage()); // never tell the visitor
            }
        }

        return $this->page('forgot-password', 'Forgot password', ['sent' => true]);
    }

    //------------------------------------ Reset password ------------------------------------//
    /** GET /reset-password?token=... */
    public function showReset(): string
    {
        $token = (string) ($_GET['token'] ?? '');

        return $this->page('reset-password', 'Reset password', [
            'token'   => $token,
            'invalid' => !Admin::isValidResetToken($token),
            'error'   => null,
        ]);
    }

    /** POST /reset-password */
    public function reset(): ?string
    {
        $request  = new Request();
        $token    = (string) $request->input('token', '');
        $password = (string) $request->input('password', '');
        $confirm  = (string) $request->input('confirm', '');

        $error = null;
        if (!Admin::isValidResetToken($token)) {
            return $this->page('reset-password', 'Reset password', ['token' => '', 'invalid' => true, 'error' => null]);
        }
        if (mb_strlen($password) < self::MIN_PASSWORD) {
            $error = 'The password must have at least ' . self::MIN_PASSWORD . ' characters.';
        } elseif ($password !== $confirm) {
            $error = 'The two passwords do not match.';
        }

        if ($error !== null) {
            http_response_code(422);
            return $this->page('reset-password', 'Reset password', ['token' => $token, 'invalid' => false, 'error' => $error]);
        }

        Admin::resetPassword($token, $password);
        Response::redirect(url('/login?reset=1'));

        return null;
    }

    //------------------------------------ Helpers ------------------------------------//
    private function page(string $view, string $title, array $data): string
    {
        return View::render('pages/' . $view, $data + [
            'title'  => $title . ' · ' . config('site_title'),
            'active' => '',
            'styles' => ['auth'],
        ]);
    }

    private function setCookie(string $value, int $expires): void
    {
        setcookie(AuthTokenService::COOKIE, $value, [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => str_starts_with((string) config('app_url'), 'https'),
            'httponly' => true,   // JavaScript can't read it
            'samesite' => 'Lax',  // not sent on cross-site POSTs
        ]);
    }
}