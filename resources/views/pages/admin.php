<?php

/** @var array|null $edit */ ?>
<h1>Admin</h1>
<!-- TODO <div class="note">No login yet: anyone who opens /admin can change the catalog.</div> -->

<div class="box" style="margin-top:0">
    <h2 style="font-size:1.3rem;margin-bottom:12px"><?= $edit ? 'Edit' : 'Add' ?> flower</h2>
    <form method="post" action="<?= e(url('/admin/save')) ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
        <div class="form-grid">
            <label>Name<input name="name" required value="<?= e($edit['name'] ?? '') ?>"></label>
            <label>Scientific name<input name="scientific_name" value="<?= e($edit['scientific_name'] ?? '') ?>"></label>
            <?php
            $default = (string) config('default_region');
            $options = $regionsEnabled ? $regionNames : [$default];
            $picked  = $regionsEnabled ? ($edit['regions'] ?? []) : [$default];
            ?>
            <fieldset class="region-pick full" <?= $regionsEnabled ? '' : 'disabled' ?>>
                <legend>Regions<?= $regionsEnabled ? '' : ' (locked to ' . e($default) . ' for now)' ?></legend>
                <?php foreach ($options as $name): ?>
                    <label>
                        <input type="checkbox" name="regions[]" value="<?= e($name) ?>" <?= in_array($name, $picked, true) ? 'checked' : '' ?>>
                        <?= e($name) ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <label>Colors (hex, comma separated)<input name="colors" placeholder="#e8a1c4, #ffffff" value="<?= e(implode(', ', $edit['colors'] ?? [])) ?>"></label>
            <label>Edible
                <select name="edible">
                    <option value="true" <?= !empty($edit['edible']) ? 'selected' : '' ?>>Yes</option>
                    <option value="false" <?= empty($edit['edible']) ? 'selected' : '' ?>>No</option>
                </select>
            </label>
            <label>Season<input name="season" value="<?= e($edit['season'] ?? '') ?>"></label>
            <label>Where to find it<input name="location" value="<?= e($edit['location'] ?? '') ?>"></label>
            <?php foreach ($platforms as $p): ?>
                <label><?= e($p['name']) ?> link (optional)
                    <input type="url" name="links[<?= (int) $p['id'] ?>]" placeholder="https://..."
                        value="<?= e($edit['links'][strtolower($p['name'])] ?? '') ?>">
                </label>
            <?php endforeach; ?>
            <?php if (!empty($edit['images'])): ?>
                <div class="full">
                    <b>Current images</b> <span class="sci">(tick the ones to remove; the first is the main image)</span>
                    <div class="img-list">
                        <?php foreach ($edit['images'] as $file): ?>
                            <label class="img-item">
                                <img src="<?= e(image_src($file)) ?>" alt="">
                                <span><input type="checkbox" name="remove_images[]" value="<?= e($file) ?>"> Remove</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <label class="full"><?= $edit ? 'Add more images' : 'Images' ?> (you can select several)
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
            </label>
            <label class="full">Description<textarea name="description" rows="2"><?= e($edit['description'] ?? '') ?></textarea></label>
            <label class="full">Benefits<textarea name="benefits" rows="2"><?= e($edit['benefits'] ?? '') ?></textarea></label>
        </div>
        <div class="row" style="margin-top:14px">
            <button class="btn" type="submit"><?= $edit ? 'Save changes' : 'Add flower' ?></button>
            <?php if ($edit): ?><a class="btn alt" href="<?= e(url('/admin')) ?>">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<h2 style="font-size:1.3rem;margin:26px 0 10px"><?= count($flowers) ?> flowers</h2>
<div class="scroll">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Regions</th>
                <th>Edible</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($flowers as $f): ?>
                <tr>
                    <td><?= e($f['name']) ?></td>
                    <td><?= e(implode(', ', $f['regions'])) ?></td>
                    <td><?= $f['edible'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <div class="actions">
                            <a class="btn alt sm" href="<?= e(url('/admin?edit=' . $f['id'])) ?>">Edit</a>
                            <form method="post" action="<?= e(url('/admin/delete')) ?>" data-confirm="<?= e('Delete "' . $f['name'] . '"?') ?>">
                                <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                <button class="btn sm" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>