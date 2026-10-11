<?php
declare(strict_types=1);

namespace App\Services;

class WhatsApp
{
    /** https://wa.me/<number>?text=<message> */
    public static function link(string $message): string
    {
        return 'https://wa.me/' . config('whatsapp_number') . '?text=' . rawurlencode($message);
    }

    /** Message sent when a visitor can't find a flower (same wording as the static model). */
    public static function missingFlowerMessage(string $name, string $region, string $location, string $notes): string
    {
        return "Hello! I found a flower that is not in the catalog.\n"
            . 'Name: ' . ($name ?: '-') . "\n"
            . 'Region: ' . ($region ?: '-') . "\n"
            . 'Location: ' . ($location ?: '-') . "\n"
            . 'Notes: ' . ($notes ?: '-') . "\n"
            . '(I will attach the photo now.)';
    }
}
