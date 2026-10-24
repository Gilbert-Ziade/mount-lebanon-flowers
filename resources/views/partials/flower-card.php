<?php

/** @var array $flower */ ?>
<a class="card" href="<?= e(url('/flowers/' . $flower['slug'])) ?>">
  <div class="thumb" style="<?= e(flower_bg($flower)) ?>"><?= flower_slide($flower, 0, 120) ?></div>
  <div class="in">
    <h3><?= e($flower['name']) ?></h3>
    <div class="sci">
      <?= e($flower['scientific_name']) ?>
    </div>
    <?= edible_tag($flower) ?>
    <!--<span class="tag"> // TODO to be removed when i want to add a multiple regions
      <?/*= e(implode(', ', $flower['regions'])) */?>
    </span>-->
    <br>
    <?= color_dots($flower) ?>
  </div>
</a>