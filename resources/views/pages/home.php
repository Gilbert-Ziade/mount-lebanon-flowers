<section class="hero">
  <div>
    <h1>Every wild flower of your village, in one place.</h1>
    <p>Browse the flowers growing in Baskinta, Chahtoul, Faraya and more. See their colors, uses, and whether you can eat them.</p>
    <div class="row">
      <a class="btn" href="<?= e(url('/flowers')) ?>">Browse all flowers</a>
      <a class="btn alt" href="<?= e(url('/missing')) ?>">Can't find a flower?</a>
    </div>
  </div>
  <div class="bouquet">
    <?php foreach ($bouquet as $flower): ?>
      <div style="<?= e(flower_bg($flower)) ?>"><?= flower_art($flower['colors'], 70) ?></div>
    <?php endforeach; ?>
  </div>
</section>

<h2>Choose a region</h2>
<div class="regions">
  <?php foreach ($regions as $region): ?>
    <a class="region" href="<?= e(url('/flowers?region=' . rawurlencode($region['name']))) ?>">
      <b><?= e($region['name']) ?></b><span><?= (int) $region['count'] ?> flowers</span>
    </a>
  <?php endforeach; ?>
</div>

<h2>Recently added</h2>
<div class="grid" style="margin-top:14px">
  <?php foreach ($recent as $flower): ?>
    <?php require BASE_PATH . '/resources/views/partials/flower-card.php'; ?>
  <?php endforeach; ?>
</div>
