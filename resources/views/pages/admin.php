<?php /** @var array|null $edit */ ?>
<h1>Admin</h1>
<div class="note">No login yet: anyone who opens /admin can change the catalog.</div>

<div class="box" style="margin-top:0">
  <h2 style="font-size:1.3rem;margin-bottom:12px"><?= $edit ? 'Edit' : 'Add' ?> flower</h2>
  <form method="post" action="<?= e(url('/admin/save')) ?>">
    <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
    <div class="form-grid">
      <label>Name<input name="name" required value="<?= e($edit['name'] ?? '') ?>"></label>
      <label>Scientific name<input name="scientific_name" value="<?= e($edit['scientific_name'] ?? '') ?>"></label>
      <label>Regions (comma separated)<input name="regions" value="<?= e(implode(', ', $edit['regions'] ?? [])) ?>"></label>
      <label>Colors (hex, comma separated)<input name="colors" placeholder="#e8a1c4, #ffffff" value="<?= e(implode(', ', $edit['colors'] ?? [])) ?>"></label>
      <label>Edible
        <select name="edible">
          <option value="true"  <?= !empty($edit['edible']) ? 'selected' : '' ?>>Yes</option>
          <option value="false" <?= empty($edit['edible']) ? 'selected' : '' ?>>No</option>
        </select>
      </label>
      <label>Season<input name="season" value="<?= e($edit['season'] ?? '') ?>"></label>
      <label>Where to find it<input name="location" value="<?= e($edit['location'] ?? '') ?>"></label>
      <label>Instagram link<input name="instagram" value="<?= e($edit['links']['instagram'] ?? '') ?>"></label>
      <label class="full">Image file names or URLs (comma separated, first one is the main image)
        <input name="images" placeholder="cyclamen-1.jpg, cyclamen-2.jpg" value="<?= e(implode(', ', $edit['images'] ?? [])) ?>">
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
    <thead><tr><th>Name</th><th>Regions</th><th>Edible</th><th></th></tr></thead>
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