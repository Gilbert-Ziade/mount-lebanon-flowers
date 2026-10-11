<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Flower;
use App\Models\Region;

class PageController
{
    public function home(): string
    {
        return View::render('pages/home', [
            'title'   => config('site_title'),
            'active'  => 'home',
            'styles'  => ['home'],
            'bouquet' => Flower::first(6),
            'regions' => Region::withCounts(),
            'recent'  => Flower::recent(4),
        ]);
    }

    public function about(): string
    {
        return View::render('pages/about', [
            'title'  => 'About · ' . config('site_title'),
            'active' => 'about',
        ]);
    }
}
