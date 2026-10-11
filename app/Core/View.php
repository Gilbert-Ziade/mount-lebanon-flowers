<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    /** Render resources/views/<view>.php inside a layout and return the HTML string. */
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): string
    {
        $base = BASE_PATH . '/resources/views/';

        // 1) render the page itself
        $content = self::capture($base . $view . '.php', $data);

        // 2) wrap it in the layout
        return self::capture($base . $layout . '.php', $data + ['content' => $content]);
    }

    private static function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
