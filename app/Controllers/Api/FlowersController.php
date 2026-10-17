<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Request;
use App\Core\Response;
use App\Models\Flower\Flower;
use App\Models\Region\Region;

class FlowersController
{
    /**
     * POST /api/v1/get-flowers
     * Body (JSON or form): { "q": "thyme", "region": "Chahtoul", "edible": "true" }  (all optional)
     */
    public function getFlowers(): void
    {
        $request = new Request();

        $edible = $request->input('edible');
        if (is_bool($edible)) {
            $edible = $edible ? 'true' : 'false';
        }

        $flowers = Flower::search(
            (string) $request->input('q', ''),
            (string) $request->input('region', ''),
            (string) $edible
        );

        Response::json([
            'data' => array_map([$this, 'present'], $flowers),
            'meta' => ['count' => count($flowers)],
        ]);
    }

    /** GET /api/v1/flowers/{slug} */
    public function show(string $slug): void
    {
        $flower = Flower::findBySlug($slug);
        if ($flower === null) {
            Response::json(['error' => 'Flower not found'], 404);
        }
        Response::json(['data' => $this->present($flower)]);
    }

    /** GET /api/v1/regions */
    public function regions(): void
    {
        Response::json(['data' => Region::withCounts()]);
    }

    /** Shape of a flower in API responses. */
    private function present(array $flower): array
    {
        return $flower + ['url' => url('/flowers/' . $flower['slug'])];
    }
}
