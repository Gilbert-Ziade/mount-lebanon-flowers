<?php
declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

/** Plain-text email over SMTP. Settings live in config('mail'). Throws on failure. */
class Mailer
{
    public static function send(string $to, string $subject, string $body): void
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = (string) config('mail.host');
        $mail->Port       = (int) config('mail.port', 587);
        $mail->SMTPAuth   = true;
        $mail->Username   = (string) config('mail.user');
        $mail->Password   = (string) config('mail.pass');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom((string) config('mail.user'), (string) config('site_name'));
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
    }
}