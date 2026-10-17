<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Flower\Flower;
use App\Services\ImageUploader;

class AdminController
{
    /** GET /admin  or  /admin?edit=ID */
    public function index(): string
    {
        $editId = (int) ($_GET['edit'] ?? 0);

        return View::render('pages/admin', [
            'title'   => 'Admin · ' . config('site_title'),
            'active'  => 'admin',
            'styles'  => ['admin'],
            'flowers' => Flower::all(),
            'edit'    => $editId ? Flower::findById($editId) : null,
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

        $data = [
            'name'            => $text('name'),
            'scientific_name' => $text('scientific_name'),
            'description'     => $text('description'),
            'benefits'        => $text('benefits'),
            'edible'          => $text('edible') === 'true' ? 1 : 0,
            'season'          => $text('season'),
            'location'        => $text('location'),
            'instagram'       => $text('instagram'),
            'regions'         => $list('regions'),
            'colors'          => $list('colors'),
            'images'          => $list('images'),
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
