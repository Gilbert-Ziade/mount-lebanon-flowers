<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response;
use App\Core\View;
use App\Models\Flower\Flower;
use App\Models\Region\Region;

class FlowerPageController
{
    /** GET /flowers?q=&region=&edible= */
    public function index(): string
    {
        $term   = trim((string) ($_GET['q'] ?? ''));
        $region = (string) ($_GET['region'] ?? '');
        $edible = (string) ($_GET['edible'] ?? '');

        return View::render('pages/catalog', [
            'title'   => 'All flowers · ' . config('site_title'),
            'active'  => 'catalog',
            'styles'  => ['catalog'],
            'flowers' => Flower::search($term, $region, $edible),
            'regions' => Region::names(),
            'term'    => $term,
            'region'  => $region,
            'edible'  => $edible,
        ]);
    }

    /** GET /flowers/{slug} */
    public function show(string $slug): string
    {
        $flower = Flower::findBySlug($slug);
        if ($flower === null) {
            return Response::notFound('Flower not found');
        }

        return View::render('pages/flower', [
            'title'  => $flower['name'] . ' · ' . config('site_title'),
            'active' => 'catalog',
            'styles' => ['flower'],
            'flower' => $flower,
        ]);
    }
}
