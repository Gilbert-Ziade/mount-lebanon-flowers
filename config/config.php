<?php
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        throw new RuntimeException(".env file not found at $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // skip comments
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'"); // strip surrounding quotes/whitespace

        if (!getenv($name)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

// Point to the .env in project root, relative to this file's location
loadEnv(__DIR__ . '/../.dev.env');

// TODO
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_PORT', getenv('DB_PORT'));
define('DB_PASS', getenv('DB_PASS'));

define('JWT_SECRET', getenv('JWT_SECRET'));

define('NO_REPLY_EMAIL', getenv('NO_REPLY_EMAIL'));
define('EMAIL_PASSWORDS', getenv('EMAIL_PASSWORDS'));

return [
    'site_name'       => 'Mount Lebanon Flora',
    'site_title'      => 'Wild Flowers of Mount Lebanon',
    'base_url'        => '',                 // '' if served at the domain root, e.g. '/flowers-site' if in a sub-folder
    'whatsapp_number' => '96170000000',      // country code first, no "+"
    'social' => [
        'instagram' => 'https://instagram.com/',
        'facebook'  => 'https://facebook.com/',
        'youtube'   => 'https://youtube.com/',
    ]
];