<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\View;
use App\Models\Region\Region;
use App\Services\WhatsApp;

class MissingFlowerController
{
    /** GET /missing : the "Can't find a flower?" form */
    public function show(): string
    {
        return View::render('pages/missing', [
            'title'   => "Can't find a flower? · " . config('site_title'),
            'active'  => 'missing',
            'styles'  => ['missing'],
            'regions' => Region::names(),
        ]);
    }

    /** POST /missing/whatsapp : build the WhatsApp link and redirect to it */
    public function send(): void
    {
        $clean = fn (string $key, int $max) => mb_substr(trim((string) ($_POST[$key] ?? '')), 0, $max);

        $region = $clean('region', 60);
        if ($region !== 'Other' && !in_array($region, Region::names(), true)) {
            $region = 'Other';
        }

        $message = WhatsApp::missingFlowerMessage(
            $clean('name', 80),
            $region,
            $clean('location', 300),
            $clean('notes', 600)
        );

        Response::redirect(WhatsApp::link($message));
    }
}
