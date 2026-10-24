<h1>All flowers</h1>
<form class="filters" method="get" action="<?= e(url('/flowers')) ?>">
  <input name="q" type="search" placeholder="Search by name or description" value="<?= e($term) ?>" aria-label="Search flowers">
  <!-- TODO remove when multiple regions 
   <select name="region" aria-label="Region" data-autosubmit>
    <option value="">All regions</option>
    <?php /*foreach ($regions as $r): ?>
      <option <?= $r === $region ? 'selected' : '' ?>><?= e($r) ?></option>
    <?php endforeach;*/ ?>
  </select>-->
  <select name="edible" aria-label="Edible" data-autosubmit>
    <option value="">Edible or not</option>
    <option value="true" <?= $edible === 'true' ? 'selected' : '' ?>>Edible</option>
    <option value="false" <?= $edible === 'false' ? 'selected' : '' ?>>Not edible</option>
  </select>
  <noscript><button class="btn sm" type="submit">Filter</button></noscript>
</form>

<?php if ($flowers): ?>
  <div class="grid">
    <?php foreach ($flowers as $flower): ?>
      <?php require BASE_PATH . '/resources/views/partials/flower-card.php'; ?>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <div class="empty">
    <p>No flower matches your search.</p>
    <p style="margin:12px 0"><a class="btn wa" href="<?= e(url('/missing')) ?>">Send us a photo on WhatsApp</a></p>
  </div>
<?php endif; ?>