<?php
function loadEnv(string $path): array
{
    if (!file_exists($path)) {
        throw new RuntimeException(".env file not found at $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $values = [];

    foreach ($lines as $line) {
        $line = trim($line);

        // skip comments
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'"); // strip surrounding quotes/whitespace

        $values[$name] = $value;
    }

    return $values;
}

/**
 * Value of a setting: the real environment variable first (local override),
 * then .dev.env. Values are kept in an array instead of putenv(), because some
 * hosts (InfinityFree) do not keep putenv() values, which left them empty.
 */
function env(string $name): string
{
    static $fromFile = null;
    // Point to the .env in project root, relative to this file's location
    $fromFile ??= loadEnv(__DIR__ . '/../.dev.env');

    return (string) (getenv($name) ?: ($fromFile[$name] ?? ''));
}

// TODO
define('DB_HOST', env('DB_HOST'));
define('DB_USER', env('DB_USER'));
define('DB_NAME', env('DB_NAME'));
define('DB_PORT', env('DB_PORT'));
define('DB_PASS', env('DB_PASS'));

define('JWT_SECRET', env('JWT_SECRET'));

define('NO_REPLY_EMAIL', env('NO_REPLY_EMAIL'));
define('EMAIL_PASSWORDS', env('EMAIL_PASSWORDS'));

// Written to storage/logs/error.log when the database settings are missing
foreach (['DB_HOST', 'DB_USER', 'DB_NAME'] as $key) {
    if (constant($key) === '') {
        \App\Core\ErrorLogger::log('WARNING', "$key is empty. Check that .dev.env is in the project root and contains it.");
    }
}

return [
    'site_name'       => 'Mount Lebanon Flora',
    'site_title'      => 'Wild Flowers of Mount Lebanon',
    'base_url'        => '',                 // '' if served at the domain root, e.g. '/flowers-site' if in a sub-folder
    'app_url'         => 'http://localhost:8000', // scheme + host only, used in emailed links; https enables Secure cookies
    'whatsapp_number' => '96170000000',      // country code first, no "+"
    'social' => [
        'instagram' => 'https://instagram.com/',
        'facebook'  => 'https://facebook.com/',
        'youtube'   => 'https://youtube.com/',
    ],
    'admin_region_select' => false,        // false = locked to default_region, true = multi-select
    'default_region'      => 'Rechmaya',

    'jwt_secret' => JWT_SECRET,
    'mail' => [
        'host' => 'smtp.gmail.com',        // TODO remove change to your SMTP provider
        'port' => 587,                     // STARTTLS
        'user' => NO_REPLY_EMAIL,
        'pass' => EMAIL_PASSWORDS,
    ],
];