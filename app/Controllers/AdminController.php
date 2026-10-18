<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Flower\Flower;
use App\Services\ImageUploader;
use App\Models\Region\Region;
use App\Models\Platform\Platform;

class AdminController
{
    /** GET /admin  or  /admin?edit=ID */
    public function index(): string
    {
        $editId = (int) ($_GET['edit'] ?? 0);

        return View::render('pages/admin', [
            'title'          => 'Admin · ' . config('site_title'),
            'active'         => 'admin',
            'styles'         => ['admin'],
            'flowers'        => Flower::all(),
            'edit'           => $editId ? Flower::findById($editId) : null,
            'regionNames'    => Region::names(),
            'regionsEnabled' => (bool) config('admin_region_select', false),
            'platforms' => Platform::all(),
        ]);
    }

    /** POST /admin/save : creates a flower, or updates it when "id" is sent */
    public function save(): void
    {
        $request = new Request();
        $text    = fn(string $key): string => trim((string) $request->input($key, ''));
        $list    = fn(string $key): array => array_values(array_unique(array_filter(
            array_map('trim', explode(',', $text($key)))
        )));
        $regions = config('admin_region_select', false)
            ? array_values(array_intersect(
                array_map('strval', (array) $request->input('regions', [])),
                Region::names()
            ))
            : [(string) config('default_region')];

        $posted = (array) $request->input('links', []);
        $links  = [];
        foreach (Platform::all() as $p) {
            $url = trim((string) ($posted[$p['id']] ?? ''));
            if ($url === '') {
                continue;                         // optional: empty = no link
            }
            if (filter_var($url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $url)) {
                $links[$p['id']] = $url;
            }
        }

        $data = [
            'name'            => $text('name'),
            'scientific_name' => $text('scientific_name'),
            'description'     => $text('description'),
            'benefits'        => $text('benefits'),
            'edible'          => $text('edible') === 'true' ? 1 : 0,
            'season'          => $text('season'),
            'location'        => $text('location'),
            'instagram'       => $text('instagram'),
            'regions'         => $regions,
            'colors'          => $list('colors'),
            'images'          => $list('images'),
            'links'           => $links,
        ];

        if ($data['name'] !== '') {
            $id = (int) $text('id');

            $data['new_images']    = ImageUploader::saveMany('images');
            $data['remove_images'] = array_map('strval', (array) ($_POST['remove_images'] ?? []));

            try {
                if ($id) {
                    foreach (Flower::update($id, $data) as $file) {
                        ImageUploader::delete($file);
                    }
                } else {
                    Flower::create($data);
                }
            } catch (\Throwable $e) {
                foreach ($data['new_images'] as $file) { // DB failed: don't leave orphan files
                    ImageUploader::delete($file);
                }
                throw $e;
            }
        }

        Response::redirect(url('/admin'));
    }

    /** POST /admin/delete */
    public function destroy(): void
    {
        $id = (int) (new Request())->input('id', 0);
        if ($id) {
            Flower::destroy($id);
        }
        Response::redirect(url('/admin'));
    }
}
