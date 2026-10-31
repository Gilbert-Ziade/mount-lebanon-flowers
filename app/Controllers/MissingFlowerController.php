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
        $enabled = (bool) config('admin_region_select', false);

        return View::render('pages/missing', [
            'title'          => "Can't find a flower? · " . config('site_title'),
            'active'         => 'missing',
            'styles'         => ['missing'],
            'regions'        => Region::names(),
            'regionsEnabled' => $enabled,
            'defaultRegion'  => (string) config('default_region'),
        ]);
    }

    /** POST /missing/whatsapp : build the WhatsApp link and redirect to it */
    public function send(): void
    {
        $clean = fn(string $key, int $max) => mb_substr(trim((string) ($_POST[$key] ?? '')), 0, $max);

        if (config('admin_region_select', false)) {
            // Selection enabled: accept a known region or "Other", anything else becomes "Other".
            $region = $clean('region', 60);
            if ($region !== 'Other' && !in_array($region, Region::names(), true)) {
                $region = 'Other';
            }
        } else {
            // Selection disabled: always the default region, whatever the browser sent.
            $region = (string) config('default_region');
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
