<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

/**
 * Writes errors to storage/logs/error.log so they can be read later for debugging.
 *
 * Records:
 *   - PHP warnings, notices and deprecations (PHP still runs its normal handling)
 *   - uncaught exceptions (visitor gets the generic error page)
 *   - fatal errors
 *   - database errors (logged from public/index.php)
 *
 * Only the request path is written, never the query string or POST data,
 * so tokens in URLs such as /reset-password?token=... are not saved.
 */
final class ErrorLogger
{
    private static bool $registered = false;
    private static bool $writing = false;

    /** Install the PHP error, exception and shutdown handlers. Call once from bootstrap. */
    public static function register(): void
    {
        if (self::$registered) {
            return;
        }
        self::$registered = true;

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /** Full path of the log file. */
    public static function file(): string
    {
        return BASE_PATH . '/storage/logs/error.log';
    }

    /** Append one entry to the log file. Never throws. */
    public static function log(string $level, string $message, ?Throwable $e = null): void
    {
        // Guard against loops, e.g. a warning raised while writing the log.
        if (self::$writing) {
            return;
        }
        self::$writing = true;

        $entry = sprintf("[%s] %s %s\n    %s\n", date('Y-m-d H:i:s'), $level, self::requestLabel(), $message);
        if ($e !== null) {
            $entry .= self::describe($e);
        }
        $entry .= "\n";

        $dir = dirname(self::file());
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (@file_put_contents(self::file(), $entry, FILE_APPEND | LOCK_EX) === false) {
            error_log('Could not write to ' . self::file() . ': ' . $message);
        }

        self::$writing = false;
    }

    /** PHP warnings, notices and deprecations. */
    public static function handleError(int $errno, string $message, string $file, int $line): bool
    {
        // Respects @-suppression and error_reporting() settings.
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $level = match (true) {
            (bool) ($errno & (E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING)) => 'WARNING',
            (bool) ($errno & (E_DEPRECATED | E_USER_DEPRECATED)) => 'DEPRECATED',
            default => 'NOTICE',
        };

        self::log($level, sprintf('%s in %s:%d', $message, $file, $line));

        // Returning false lets PHP run its normal handling too (display / error_log).
        return false;
    }

    /** Uncaught exceptions: log them and show the generic 500 page. */
    public static function handleException(Throwable $e): void
    {
        self::log('ERROR', 'Uncaught exception', $e);

        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }

        try {
            echo View::render('errors/server', ['title' => 'Server error']);
        } catch (Throwable) {
            echo 'Something went wrong.';
        }
    }

    /** Fatal errors (PHP cannot pass these to the error handler). */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error === null) {
            return;
        }

        $fatal = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
        if ($error['type'] & $fatal) {
            self::log('FATAL', sprintf('%s in %s:%d', $error['message'], $error['file'], $error['line']));
        }
    }

    /** Exception class, message and location for each exception in the chain, then the stack trace. */
    private static function describe(Throwable $e): string
    {
        $out = '';
        for ($current = $e; $current !== null; $current = $current->getPrevious()) {
            $out .= sprintf("    %s: %s (%s:%d)\n", $current::class, $current->getMessage(), $current->getFile(), $current->getLine());
        }

        return $out . '    ' . str_replace("\n", "\n    ", $e->getTraceAsString()) . "\n";
    }

    /** "GET /flowers" for web requests, "CLI" otherwise. Path only, no query string. */
    private static function requestLabel(): string
    {
        if (PHP_SAPI === 'cli') {
            return 'CLI';
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? '-';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        return $method . ' ' . $path;
    }
}
