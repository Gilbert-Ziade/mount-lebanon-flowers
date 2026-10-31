<?php
$first = flower_slides($flower)[0] ?? null;
$bg    = is_string($first) ? '--bg:url(' . image_thumb($first) . ');' : '';
?>
<a class="card" href="<?= e(url('/flowers/' . $flower['slug'])) ?>">
  <div class="thumb" style="<?= e($bg . flower_bg($flower)) ?>">
    <span class="badge <?= $flower['edible'] ? '' : 'no' ?>"><?= $flower['edible'] ? 'Edible' : 'Not edible' ?></span>
    <?= flower_slide($flower, 0, 120, true) ?>
  </div>
  <div class="in">
    <h3><?= e($flower['name']) ?></h3>
    <div class="sci"><?= e($flower['scientific_name']) ?></div>
    <?= color_dots($flower) ?>
  </div>
</a>