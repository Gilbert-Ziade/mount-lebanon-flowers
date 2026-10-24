<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Tiny PSR-4 style autoloader: App\Core\Router -> app/Core/Router.php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// Autoloader for Firebase JWT
spl_autoload_register(function (string $class): void {
    $prefix = 'Firebase\\JWT\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $file = BASE_PATH . '/utils/firebase/src/'
        . str_replace('\\', '/', substr($class, strlen($prefix)))
        . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});


// Autoloader for PHPMailer
spl_autoload_register(function (string $class): void {
    $prefix = 'PHPMailer\\PHPMailer\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $file = BASE_PATH . '/utils/PHPMailer/src/'
        . str_replace('\\', '/', substr($class, strlen($prefix)))
        . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});


require BASE_PATH . '/app/helpers.php';
